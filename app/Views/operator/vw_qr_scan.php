<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
	<!-- User Profile Card -->
	<div class="card overflow-hidden">
		<div class="card-body p-0">
			<img src="<?= base_url('assets/images/backgrounds/profilebg.jpg'); ?>" class="img-fluid"
				alt="Profile Background">
			<div class="row align-items-center">
				<div class="col-lg-12 mt-n3 text-center">
					<div class="mt-n5">
						<div class="d-flex align-items-center justify-content-center mb-2">
							<div class="d-flex align-items-center justify-content-center"
								style="width: 110px; height: 110px;">
								<div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
									style="width: 100px; height: 100px;">
									<img src="<?= profile_image_url($user_info['name'] ?? null) ?>"
										class="w-100 h-100" alt="<?= esc($user_info['name']) ?>">
								</div>
							</div>
						</div>
						<div class="text-center">
							<h5 class="fs-5 mb-0 fw-semibold text-capitalize"><?= esc($user_info['name']) ?></h5>
							<p class="mb-0 fs-4"><?= esc($user_info['user_role']) ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card overflow-hidden p-3" id="camera_container">
		<h3 class="card-title text-center text-uppercase fw-bold d-flex align-items-center justify-content-center mb-3">
			<?= esc($locations['location_name']) ?>
		</h3>
		<div class="card-footer d-flex">
			<a class="btn btn-primary d-flex align-items-center px-1 py-2" href="<?= base_url('operator') ?>">
				<iconify-icon icon="fa7-solid:arrow-left" width="20" height="20" class="me-1"></iconify-icon>
				Kembali
			</a>
		</div>
		<div class="card-body d-flex flex-column p-2 justify-content-center align-items-center">
			<div id="reader" style="width:250px;"></div>
			<div class="d-flex">
				<button id="rotate_camera" class="btn btn-sm btn-primary my-2">Putar Kamera</button>
				<button id="stop_camera" class="btn btn-sm btn-danger my-2">Stop Kamera</button>
				<button id="start_camera" class="btn btn-sm btn-success my-2">Mulai Kamera</button>
			</div>
		</div>
	</div>

	<div class="card d-none" id="card_result">
		<div class="card-header bg-light">
			<h5 class="mb-0">📋 Pilih Item yang Sudah Dikerjakan</h5>
		</div>
		<div class="card-body w-100">
			<?php foreach ($items as $item): ?>
				<button class="btn btn-outline-primary btn-sm w-100 my-1 text-start position-relative"
					id="item_<?= $item['item_id'] ?>"
					onclick="openActionModal('<?= $item['item_id'] ?>', '<?= esc($item['item_name']) ?>')">
					<i class="fas fa-check-square"></i> <?= esc($item['item_name']) ?>
					<?php if (in_array($item['item_id'], $revision_items ?? [])): ?>
						<span
							class="position-absolute top-0 start-100 translate-middle badge bg-warning text-dark rounded-pill">!</span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
		<div class="card-footer d-flex justify-content-end gap-2">
			<button class="btn btn-danger d-flex align-items-center px-3 py-2" onclick="cancelData()">
				<iconify-icon icon="fa7-solid:ban" width="20" height="20" class="me-1"></iconify-icon>
				Batal
			</button>
			<button class="btn btn-success d-flex align-items-center px-3 py-2" onclick="submitData()">
				<iconify-icon icon="fa7-solid:save" width="20" height="20" class="me-1"></iconify-icon>
				Simpan
			</button>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
	// Global variables
	const actionData = JSON.parse(localStorage.getItem('action_data')) || {};
	const locationId = '<?= $locations['location_id'] ?>';
	const locationName = '<?= esc($locations['location_name']) ?>';
	const userId = '<?= $user_info['user_id'] ?>';
	const csrfToken = '<?= csrf_token() ?>';
	const csrfHash = '<?= csrf_hash() ?>';
	const itemIds = <?= json_encode(array_column($items, 'item_id')) ?>;
	const rotateCameraButton = document.getElementById('rotate_camera');
	const stopCameraButton = document.getElementById('stop_camera');
	const startCameraButton = document.getElementById('start_camera');



	let qrScanner;
	let cameraState = 1;

	// Initialize page
	document.addEventListener('DOMContentLoaded', function () {
		// Ensure action data starts empty when entering a room
		localStorage.removeItem('action_data');

		startCamera('environment');
		updateItemStatus();
	});

	// Clear action_data when page is restored from bfcache (back/forward)
	window.addEventListener('pageshow', function (e) {
		if (e.persisted) {
			localStorage.removeItem('action_data');
			updateItemStatus();
		}
	});

	// Also clear when navigating away (pagehide) to avoid stale state
	window.addEventListener('pagehide', function () {
		localStorage.removeItem('action_data');
	});

	rotateCameraButton.addEventListener('click', function () {
		if (cameraState == 1) {
			qrScanner.stop().then(() => {
				startCamera('user');
				cameraState = 0;
			}).catch(err => {
				toastr.error('Gagal memutar kamera: ' + err, 'Error');
			});
		} else {
			qrScanner.stop().then(() => {
				startCamera('environment');
				cameraState = 1;
			}).catch(err => {
				toastr.error('Gagal memutar kamera: ' + err, 'Error');
			});
		}
	});

	stopCameraButton.addEventListener('click', function () {
		qrScanner.stop().then(() => {
			$('#reader').addClass('d-none');
		}).catch(err => {
			toastr.error('Gagal menghentikan kamera: ' + err, 'Error');
		});
	});

	startCameraButton.addEventListener('click', function () {
		$('#reader').removeClass('d-none');
		startCamera('environment');
		cameraState = 1;
	});



	/**
	 * Start QR camera and scan location
	 */
	function startCamera(facing_mode) {
		qrScanner = new Html5Qrcode('reader');
		Html5Qrcode.getCameras()
			.then(devices => {
				if (!devices || devices.length === 0) {
					toastr.error('Kamera tidak ditemukan', 'Error');
					return;
				}

				const cameraId = devices[0].id;
				qrScanner.start(
					{ facingMode: facing_mode },
					{ fps: 10, qrbox: { width: 150, height: 150 } },
					onQrCodeScanned,
					onScanError
				).catch(err => {
					toastr.error('Gagal memulai pemindaian QR: ' + err, 'Error');
				});
			})
			.catch(err => {
				toastr.error('Gagal mengakses kamera: ' + err, 'Error');
			});
	}

	/**
	 * Handle QR code scan result
	 */
	function onQrCodeScanned(decodedText) {
		try {
			const result = JSON.parse(decodedText);

			if (result.lokasi.toLowerCase() !== locationName.toLowerCase()) {
				toastr.warning('Lokasi QR tidak sesuai. Silakan pindai QR lokasi yang benar.', 'Lokasi Tidak Sesuai');
				return;
			}

			// Save scanned data and show items
			localStorage.setItem('scanned_qr', decodedText);
			qrScanner.stop();
			$('#camera_container').addClass('d-none');
			$('#card_result').removeClass('d-none');
		} catch (e) {
			toastr.error('Format QR tidak valid', 'Error');
		}
	}

	/**
	 * Handle scan errors (usually just scanning failures, not QR codes)
	 */
	function onScanError(error) {
		// Silently ignore non-QR-code scan attempts
	}

	/**
	 * Open modal to select actions for an item
	 */
	function openActionModal(itemId, itemName) {
		$.ajax({
			url: '<?= base_url('operator/modal'); ?>',
			type: 'POST',
			data: {
				id: itemId,
				location_id: locationId,
				type: 'detail',
				[csrfToken]: csrfHash
			},
			success: function (response) {
				$('#bs_modal_md #md_modal_title').html(response.title);
				$('#bs_modal_md #md_modal_body').html(response.html);
				$('#bs_modal_md').modal('show');

				// Update UI after modal is closed
				$('#bs_modal_md').on('hidden.bs.modal', function () {
					updateItemStatus();
				});
			},
			error: function () {
				toastr.error('Gagal membuka pilihan aksi', 'Error');
			}
		});
	}

	/**
	 * Cancel and go back to dashboard
	 */
	function cancelData() {
		if (confirm('Apakah Anda yakin ingin membatalkan? Data yang tidak disimpan akan hilang.')) {
			localStorage.removeItem('action_data');
			localStorage.removeItem('scanned_qr');
			window.location.href = '<?= base_url('operator'); ?>';
		}
	}

	/**
	 * Submit all selected actions
	 */
	function submitData() {
		const currentActionData = JSON.parse(localStorage.getItem('action_data')) || {};

		if (!isAllItemsCompleted()) {
			toastr.warning('Semua item harus memiliki action yang dipilih sebelum menyimpan.', 'Peringatan');
			return;
		}

		const submissions = buildSubmissions(currentActionData);

		if (submissions.length === 0) {
			toastr.info('Tidak ada data untuk disimpan', 'Info');
			return;
		}

		// Disable button and show loading state
		const btn = event.target;
		btn.disabled = true;
		const originalText = btn.innerHTML;
		btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

		$.ajax({
			url: '<?= base_url('operator/add'); ?>',
			type: 'POST',
			data: {
				[csrfToken]: csrfHash,
				submissions: JSON.stringify(submissions),
				user_id: userId
			},
			success: function (response) {
				toastr.success('Data berhasil disimpan', 'Berhasil');

				// Clear data
				localStorage.removeItem('action_data');
				localStorage.removeItem('scanned_qr');

				setTimeout(() => {
					window.location.href = '<?= base_url('operator'); ?>';
				}, 1500);
			},
			error: function (xhr) {
				btn.disabled = false;
				btn.innerHTML = originalText;
				const errorMsg = xhr.responseJSON?.message || 'Gagal menyimpan data';
				toastr.error(errorMsg, 'Error');
			}
		});
	}

	/**
	 * Build submissions array from action data
	 */
	function buildSubmissions(currentActionData) {
		const submissions = [];
		const date = new Date().toISOString().split('T')[0];

		for (let loc in currentActionData) {
			for (let item in currentActionData[loc]) {
				for (let act in currentActionData[loc][item]) {
					submissions.push({
						date: date,
						location_id: loc,
						item_id: item,
						action_id: act,
						is_cleaned: true,
						is_revise: false,
						revise_description: '',
						status: 'pending',
						user_id: userId
					});
				}
			}
		}

		return submissions;
	}

	/**
	 * Update visual status of items (strikethrough when completed)
	 */
	function updateItemStatus() {
		const currentActionData = JSON.parse(localStorage.getItem('action_data')) || {};

		itemIds.forEach((itemId) => {
			const $btn = $('#item_' + itemId);

			if (currentActionData[locationId] &&
				currentActionData[locationId][itemId] &&
				Object.keys(currentActionData[locationId][itemId]).length > 0) {
				$btn.addClass('active').css('opacity', '0.7');
			} else {
				$btn.removeClass('active').css('opacity', '1');
			}
		});
	}

	/**
	 * Check if all items have been completed
	 */
	function isAllItemsCompleted() {
		const currentActionData = JSON.parse(localStorage.getItem('action_data')) || {};

		for (const itemId of itemIds) {
			if (!currentActionData[locationId] ||
				!currentActionData[locationId][itemId] ||
				Object.keys(currentActionData[locationId][itemId]).length === 0) {
				return false;
			}
		}

		return true;
	}
</script>
<?= $this->endSection() ?>
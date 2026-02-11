<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();

        try {
            // Truncate the table
            $this->db->table('items')->truncate();

            // Read data from Excel
            $excelPath = ROOTPATH . 'datasource.xlsx';
            if (!file_exists($excelPath)) {
                throw new \Exception("datasource.xlsx not found at " . $excelPath);
            }

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($excelPath);
            // Support both legacy sheet name and current
            $sheetNames = ['items', 'm_items'];
            $sheet = null;
            foreach ($sheetNames as $s) {
                $sheet = $spreadsheet->getSheetByName($s);
                if ($sheet)
                    break;
            }
            if (!$sheet) {
                throw new \Exception("Excel sheet for items not found (looked for: " . implode(', ', $sheetNames) . ")");
            }
            $data = $sheet->toArray();

            $items = [];

            // Skip header row (index 0)
            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];

                // Skip empty rows
                if (empty($row[1]) || empty($row[2])) {
                    continue;
                }

                $items[] = [
                    'room_id' => (int) $row[1],
                    'name' => trim($row[2]),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            // Insert items
            if (!empty($items)) {
                $this->db->table('items')->insertBatch($items);
                echo "✓ Seeded " . count($items) . " items\n";
            }
        } catch (\Exception $e) {
            echo "✗ Error seeding items: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            $this->db->enableForeignKeyChecks();
        }
    }
}

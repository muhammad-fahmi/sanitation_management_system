<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();

        try {
            // Truncate the table
            $this->db->table('rooms')->truncate();

            // Read data from Excel
            $excelPath = ROOTPATH . 'datasource.xlsx';
            if (!file_exists($excelPath)) {
                throw new \Exception("datasource.xlsx not found at " . $excelPath);
            }

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($excelPath);
            // Support both legacy sheet name and current
            $sheetNames = ['rooms', 'm_locations', 'locations'];
            $sheet = null;
            foreach ($sheetNames as $s) {
                $sheet = $spreadsheet->getSheetByName($s);
                if ($sheet)
                    break;
            }
            if (!$sheet) {
                throw new \Exception("Excel sheet for locations not found (looked for: " . implode(', ', $sheetNames) . ")");
            }
            $data = $sheet->toArray();

            $locations = [];

            // Skip header row (index 0)
            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];

                // Skip empty rows
                if (empty($row[1])) {
                    continue;
                }

                $locations[] = [
                    'name' => trim($row[1]),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            // Insert locations
            if (!empty($locations)) {
                $this->db->table('rooms')->insertBatch($locations);
                echo "✓ Seeded " . count($locations) . " locations\n";
            }
        } catch (\Exception $e) {
            echo "✗ Error seeding locations: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            $this->db->enableForeignKeyChecks();
        }
    }
}

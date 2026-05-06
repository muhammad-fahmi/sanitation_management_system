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
            $this->db->table('m_locations')->truncate();

            // Read data from Excel
            $excelPath = ROOTPATH . 'datasource.xlsx';
            if (!file_exists($excelPath)) {
                throw new \Exception("datasource.xlsx not found at " . $excelPath);
            }

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($excelPath);
            $sheet = $spreadsheet->getSheetByName('m_locations');
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
                    'location_name' => trim($row[1]),
                ];
            }

            // Insert locations
            if (!empty($locations)) {
                $this->db->table('m_locations')->insertBatch($locations);
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

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
            $this->db->table('m_items')->truncate();

            // Read data from Excel
            $excelPath = ROOTPATH . 'datasource.xlsx';
            if (!file_exists($excelPath)) {
                throw new \Exception("datasource.xlsx not found at " . $excelPath);
            }

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($excelPath);
            $sheet = $spreadsheet->getSheetByName('m_items');
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
                    'location_id' => (int) $row[1],
                    'item_name' => trim($row[2]),
                ];
            }

            // Insert items
            if (!empty($items)) {
                $this->db->table('m_items')->insertBatch($items);
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

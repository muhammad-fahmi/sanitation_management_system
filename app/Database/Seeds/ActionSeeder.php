<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ActionSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();

        try {
            // Truncate the table
            $this->db->table('actions')->truncate();

            // Read data from Excel
            $excelPath = ROOTPATH . 'datasource.xlsx';
            if (!file_exists($excelPath)) {
                throw new \Exception("datasource.xlsx not found at " . $excelPath);
            }

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($excelPath);
            $sheet = $spreadsheet->getSheetByName('m_actions');
            $data = $sheet->toArray();

            $actions = [];

            // Skip header row (index 0)
            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];

                // Skip empty rows
                if (empty($row[2]) || empty($row[3])) {
                    continue;
                }

                $actions[] = [
                    'item_id' => (int) $row[2],
                    'name' => trim($row[3]),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            // Insert actions
            if (!empty($actions)) {
                $this->db->table('actions')->insertBatch($actions);
                echo "✓ Seeded " . count($actions) . " actions\n";
            }
        } catch (\Exception $e) {
            echo "✗ Error seeding actions: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            $this->db->enableForeignKeyChecks();
        }
    }
}

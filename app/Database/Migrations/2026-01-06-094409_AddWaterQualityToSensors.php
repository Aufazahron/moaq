<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaterQualityToSensors extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sensors', [
            'water_quality' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'chamber',
                'comment' => 'Status kualitas air: Bagus atau Kurang Bagus'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sensors', 'water_quality');
    }
}

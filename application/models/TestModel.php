<?php

class TestModel extends CI_Model
{
    public function getNilaiSkripsiData()
    {
        $select = array(
            'nilaiskripsi',
            'COUNT(nilaiskripsi) as jumlah'
        );

        $having = array(
            'nilaiskripsi >' => 76,
            'COUNT(nilaiskripsi) >' => 10,
        );

        $query = $this->db->select($select)
            ->group_by("nilaiskripsi")
            ->having($having)
            ->order_by("nilaiskripsi", "ASC")
            ->get('akademik.ak_nilaiskripsi');

        // $query = $this->db->query("SELECT nilaiskripsi, COUNT(nilaiskripsi) FROM akademik.ak_nilaiskripsi GROUP BY nilaiskripsi HAVING nilaiskripsi > 76 AND COUNT(nilaiskripsi) > 10");

        return $query->result_array(); // or $query->result(); if you want to return some objects
    }

    public function getAverageIPKData(?String $unitFilter, ?String $graduateTimeFilter, ?String $firstYearFilter, ?String $lastYearFilter)
    {
        $tableSkripsi = 'akademik.ak_skripsi';
        $joinQueryTableSkripsi = 'ak_yudisium.nim = ak_skripsi.nim';
        $tableMahasiswa = 'akademik.ak_mahasiswa';
        $joinQueryTableMahasiswa = 'ak_yudisium.nim = ak_mahasiswa.nim';
        $tableUjianTA = 'akademik.ak_ujianta';
        $joinQueryTableUjianTA = 'ak_skripsi.idskripsi = ak_ujianta.idskripsi';
        $whereYearFilter = '';
        $whereUnitFilter = '';
        $whereGraduateTimeFilter = '';

        // Cek lagi di join dll, mungkin ada yg beda sama SQLnya
        // Nanti diubah di sini, atau di bagian query
        if ($graduateTimeFilter == 'sklulusujian') {
            $selectGraduateTime = "SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) AS tahun_lulus";
            $whereGraduateTimeFilter = "ak_skripsi.idtahap = '5'";
        } else {
            $selectGraduateTime = "SUBSTRING(CAST(ak_ujianta.tglujian AS TEXT), 1, 4) AS tahun_lulus";
            $whereGraduateTimeFilter = "ak_ujianta.idtahap = '5'";
        }

        $select = array(
            $selectGraduateTime,
            "ROUND(AVG(ak_yudisium.ipklulusan), 2) AS ipk_lulusan",
            "COUNT(CASE WHEN ak_yudisium.ipklulusan BETWEEN 2.00 AND 2.50 THEN ak_yudisium.ipklulusan END) AS range_ipk_lulusan_1",
            "COUNT(CASE WHEN ak_yudisium.ipklulusan BETWEEN 2.51 AND 3.00 THEN ak_yudisium.ipklulusan END) AS range_ipk_lulusan_2",
            "COUNT(CASE WHEN ak_yudisium.ipklulusan BETWEEN 3.01 AND 3.50 THEN ak_yudisium.ipklulusan END) AS range_ipk_lulusan_3",
            "COUNT(CASE WHEN ak_yudisium.ipklulusan BETWEEN 3.51 AND 4.00 THEN ak_yudisium.ipklulusan END) AS range_ipk_lulusan_4",
            "COUNT(DISTINCT(ak_skripsi.nim)) AS jumlah_mahasiswa",
        );


        if (isset($unitFilter)) {

            $tableUnit = 'ref.ms_unit';
            $joinQueryTableUnit = 'ak_mahasiswa.idunit = ms_unit.idunit';

            // Add some escape builder to avoid injection
            if (strlen($unitFilter) == 1) {
                $whereUnitFilter = "SUBSTRING(ms_unit.idunit, 1, 1) = '$unitFilter'";
            } else if (strlen($unitFilter) == 3) {
                $whereUnitFilter = "SUBSTRING(ms_unit.idunit, 1, 3) = '$unitFilter'";
            } else {
                $whereUnitFilter = "SUBSTRING(ms_unit.idunit, 1, 7) = '$unitFilter'";
            }

            if ((isset($firstYearFilter) && $firstYearFilter != '-') && (isset($lastYearFilter) && $lastYearFilter != '-')) {
                if ($graduateTimeFilter == 'sklulusujian') {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) as INT) 
                    BETWEEN $firstYearFilter AND $lastYearFilter";
                } else {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_ujianta.tglujian AS TEXT), 1, 4) as INT) 
                    BETWEEN $firstYearFilter AND $lastYearFilter";
                }
            } else if ((isset($firstYearFilter) && $firstYearFilter != '-') && $lastYearFilter == '-') {
                if ($graduateTimeFilter == 'sklulusujian') {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) as INT) >= $firstYearFilter";
                } else {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_ujianta.tglujian AS TEXT), 1, 4) as INT) >= $firstYearFilter";
                }
            } else if ($firstYearFilter == '-' && (isset($lastYearFilter) && $lastYearFilter != '-')) {
                if ($graduateTimeFilter == 'sklulusujian') {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) as INT) <= $lastYearFilter";
                } else {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_ujianta.tglujian AS TEXT), 1, 4) as INT) <= $lastYearFilter";
                }
            } else {
                if ($graduateTimeFilter == 'sklulusujian') {
                    $query = $this->db->select($select)
                        ->join($tableSkripsi, $joinQueryTableSkripsi)
                        ->join($tableMahasiswa, $joinQueryTableMahasiswa)
                        ->join($tableUnit, $joinQueryTableUnit)
                        ->where("$whereUnitFilter AND $whereGraduateTimeFilter AND SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) IS NOT NULL")
                        ->group_by("1")
                        ->order_by("1", "ASC")
                        ->get('akademik.ak_yudisium');
    
                    return $query->result_array();
                }
    
                $query = $this->db->select($select)
                    ->join($tableSkripsi, $joinQueryTableSkripsi)
                    ->join($tableMahasiswa, $joinQueryTableMahasiswa)
                    ->join($tableUnit, $joinQueryTableUnit)
                    ->join($tableUjianTA, $joinQueryTableUjianTA)
                    ->where("$whereUnitFilter AND $whereGraduateTimeFilter")
                    ->group_by("1")
                    ->order_by("1", "ASC")
                    ->get('akademik.ak_yudisium');

                return $query->result_array();
            }

            if ($graduateTimeFilter == 'sklulusujian') {
                $query = $this->db->select($select)
                    ->join($tableSkripsi, $joinQueryTableSkripsi)
                    ->join($tableMahasiswa, $joinQueryTableMahasiswa)
                    ->join($tableUnit, $joinQueryTableUnit)
                    ->where("$whereUnitFilter AND $whereYearFilter AND SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) IS NOT NULL")
                    ->group_by("1")
                    ->order_by("1", "ASC")
                    ->get('akademik.ak_yudisium');

                return $query->result_array();
            }


            $query = $this->db->select($select)
                ->join($tableSkripsi, $joinQueryTableSkripsi)
                ->join($tableMahasiswa, $joinQueryTableMahasiswa)
                ->join($tableUnit, $joinQueryTableUnit)
                ->join($tableUjianTA, $joinQueryTableUjianTA)
                ->where("$whereUnitFilter AND $whereYearFilter")
                ->group_by("1")
                ->order_by("1", "ASC")
                ->get("akademik.ak_yudisium");

            return $query->result_array();
        } else {
            if ((isset($firstYearFilter) && $firstYearFilter != '-') && (isset($lastYearFilter) && $lastYearFilter != '-')) {
                if ($graduateTimeFilter == 'sklulusujian') {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) as INT) 
                    BETWEEN $firstYearFilter AND $lastYearFilter";
                } else {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_ujianta.tglujian AS TEXT), 1, 4) as INT) 
                    BETWEEN $firstYearFilter AND $lastYearFilter";
                }
            } else if ((isset($firstYearFilter) && $firstYearFilter != '-') && $lastYearFilter == '-') {
                if ($graduateTimeFilter == 'sklulusujian') {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) as INT) >= $firstYearFilter";
                } else {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_ujianta.tglujian AS TEXT), 1, 4) as INT) >= $firstYearFilter";
                }
            } else if ($firstYearFilter == '-' && (isset($lastYearFilter) && $lastYearFilter != '-')) {
                if ($graduateTimeFilter == 'sklulusujian') {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) as INT) <= $lastYearFilter";
                } else {
                    $whereYearFilter = "$whereGraduateTimeFilter AND CAST(SUBSTRING(CAST(ak_ujianta.tglujian AS TEXT), 1, 4) as INT) <= $lastYearFilter";
                }
            } else {
                if ($graduateTimeFilter == 'sklulusujian') {
                    $query = $this->db->select($select)
                        ->join($tableSkripsi, $joinQueryTableSkripsi)
                        ->join($tableMahasiswa, $joinQueryTableMahasiswa)
                        ->where("$whereGraduateTimeFilter AND SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) IS NOT NULL")
                        ->group_by("1")
                        ->order_by("1", "ASC")
                        ->get('akademik.ak_yudisium');

                    return $query->result_array();
                }

                $query = $this->db->select($select)
                    ->join($tableSkripsi, $joinQueryTableSkripsi)
                    ->join($tableMahasiswa, $joinQueryTableMahasiswa)
                    ->join($tableUjianTA, $joinQueryTableUjianTA)
                    ->where($whereGraduateTimeFilter)
                    ->group_by("1")
                    ->order_by("1", "ASC")
                    ->get('akademik.ak_yudisium');

                return $query->result_array();
            }
        }

        // echo json_encode([$firstYearFilter, $lastYearFilter]);

        if ($graduateTimeFilter == 'sklulusujian') {
            $query = $this->db->select($select)
                ->join($tableSkripsi, $joinQueryTableSkripsi)
                ->join($tableMahasiswa, $joinQueryTableMahasiswa)
                ->where("$whereYearFilter AND SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) IS NOT NULL")
                ->group_by("1")
                ->order_by("1", "ASC")
                ->get('akademik.ak_yudisium');

            return $query->result_array();
        }

        $query = $this->db->select($select)
            ->join($tableSkripsi, $joinQueryTableSkripsi)
            ->join($tableMahasiswa, $joinQueryTableMahasiswa)
            ->join($tableUjianTA, $joinQueryTableUjianTA)
            ->where($whereYearFilter)
            ->group_by("1")
            ->order_by("1", "ASC")
            ->get('akademik.ak_yudisium');

        // $query = $this->db->query("SELECT SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) AS tahun_lulus, ROUND(AVG(ipklulusan), 2) AS ipk_lulusan FROM akademik.ak_yudisium GROUP BY 1 ORDER BY 1");

        // $query2 = $this->db->query("SELECT SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) AS tahun_lulus, ROUND(AVG(ipklulusan), 2) AS ipk_lulusan FROM akademik.ak_yudisium GROUP BY 1 HAVING CAST(SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) as INT) BETWEEN 2010 AND 2017 ORDER BY 1");

        return $query->result_array();
    }

    public function getGraduateYear()
    {
        $query = $this->db->select('SUBSTRING(CAST(ak_skripsi.tglsklulusujian AS TEXT), 1, 4) AS tahun_lulus')
            ->where("ak_skripsi.idtahap = '5' AND ak_skripsi.tglsklulusujian IS NOT NULL")
            ->group_by("1")
            ->order_by("1", "ASC")
            ->get('akademik.ak_skripsi');

        return $query->result_array();
    }

    public function getFacultiesNames()
    {
        // $query = $this->db->query("SELECT idunit, namaunit FROM ref.ms_unit WHERE jenisunit = 'F' ORDER BY idunit ASC");
        $query = $this->db->select("idunit, namaunit")
            ->where("jenisunit = 'F'")
            ->order_by("idunit", "ASC")
            ->get('ref.ms_unit');
        return $query->result_array(); // or $query->result(); if you want to return some objects

    }

    public function getJurusanNames($unitFilter)
    {

        // $query = $this->db->query("SELECT idunit, CONCAT(idunit, ' - ', namaunit) AS namaunit FROM ref.ms_unit WHERE jenisunit = 'J' AND SUBSTRING(idunit, 1, 1) = '$unitFilter' ORDER BY idunit ASC");
        $query = $this->db->select("idunit, CONCAT(idunit, ' - ', namaunit) AS namaunit")
            ->where(["jenisunit" => "J", "SUBSTRING(idunit, 1, 1) = " => $unitFilter])
            ->order_by("idunit", "ASC")
            ->get('ref.ms_unit');
        return $query->result_array(); // or $query->result(); if you want to return some objects

    }

    public function getProdiNames($unitFilter)
    {

        // $query = $this->db->query("SELECT idunit, namaunit FROM ref.ms_unit WHERE jenisunit = 'P' AND SUBSTRING(idunit, 1, 3) = '$unitFilter' ORDER BY idunit ASC");
        $query = $this->db->select("idunit, namaunit")
            ->where(["jenisunit" => "P", "SUBSTRING(idunit, 1, 3) = " => $unitFilter])
            ->order_by("idunit", "ASC")
            ->get('ref.ms_unit');
        return $query->result_array(); // or $query->result(); if you want to return some objects

    }
}

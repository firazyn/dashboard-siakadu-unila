<?php
defined('BASEPATH') or exit('No direct script access allowed');
include("fusioncharts/fusioncharts.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
    <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
</head>

<body>

    <section>
        <h1>Dashboard Siakadu</h1>
        <h3>Rata-rata IPK Lulusan</h3>
    </section>

    <section>

        <label for="filter_jenjang" id="label_filter_jenjang">Pilih Jenjang</label>

        <select name="filter_jenjang" id="filter_jenjang">
            <option value="universitas">Universitas</option>
            <option value="fakultas">Fakultas</option>
            <option value="jurusan">Jurusan</option>
            <option value="prodi">Program Studi</option>
        </select>

        <br>

        <label for="filter_faculty" id="label_filter_faculty" hidden>Pilih Fakultas</label>

        <select name="filter_faculty" id="filter_faculty" hidden>
        </select>

        <br>

        <label for="filter_jurusan" id="label_filter_jurusan" hidden>Pilih Jurusan</label>

        <select name="filter_jurusan" id="filter_jurusan" hidden disabled>
            <option value="-">Pilih Jurusan</option>
        </select>

        <br>

        <label for="filter_prodi" id="label_filter_prodi" hidden>Pilih Prodi</label>

        <select name="filter_prodi" id="filter_prodi" hidden disabled>
            <option value="-">Pilih Program Studi</option>
        </select>

        <br>

        <label for="filter_ipk_first_year">Pilih Tahun Awal</label>

        <select name="filter_ipk_first_year" id="filter_ipk_first_year">
            <option value="-">-</option>
            <?php foreach ($graduateYear as $gy) : ?>
                <option value="<?= $gy['tahun_lulus'] ?>"><?= $gy['tahun_lulus'] ?></option>
            <?php endforeach; ?>
        </select>

        <label for="filter_ipk_last_year">Pilih Tahun Akhir</label>

        <select name="filter_ipk_last_year" id="filter_ipk_last_year">
            <option value="-">-</option>
            <?php foreach ($graduateYear as $gy) : ?>
                <option value="<?= $gy['tahun_lulus'] ?>"><?= $gy['tahun_lulus'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <input type="radio" name="filter_graduate_time" value="tanggalujian" checked="checked">
        <label for="filter_graduate_time" name="filter_graduate_time_label">Tanggal Ujian Skripsi</label>
        <input type="radio" name="filter_graduate_time" value="sklulusujian">
        <label for="filter_graduate_time" name="filter_graduate_time_label">Tanggal SK Lulus Ujian</label><br>

        <input type="button" name="filter_ipk_button" id="filter_ipk_button" value="Filter" onclick="updateData()" />
    </section>

    <section>
        <table border="1" id="table_ipk">
            <thead>
                <th>Tahun Lulus</th>
                <th>Rata-rata IPK Lulusan</th>
            </thead>

            <tbody>
                <?php foreach ($averageIPKLulusan as $avgIPK) : ?>
                    <tr>
                        <td><?= $avgIPK['tahun_lulus'] ?></td>
                        <td><?= $avgIPK['ipk_lulusan'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="average_IPK_lulusan_chart" style="width: 100%; height: 100%;"></div>
    </section>

    <section>
        <?php

        // Chart Configuration stored in Associative Array
        $configAverageIPK = array(
            "chart" => array(
                "caption" => "Column Chart Rata-rata IPK Lulusan",
                "subCaption" => "Per Tahun",
                "xAxisName" => "Range IPK",
                "yAxisName" => "Jumlah Mahasiswa",
                "yAxisMinValue" => "3",
                "showPercentValues" => "0",
                "showValues" => "1",
                "formatNumberScale" => "0",
                "paletteColors" => "#3f6fbe, #f9e395, #040f60, #af586a, #c7a1cd, #65365f, #EED17F, #2C560A, #97CBE7, #074868, #B0D67A, #DD9D82",
                "theme" => "fusion"
            ),

            // "trendlines" => array(
            //     [
            //         'line' =>
            //         array(
            //             [
            //                 'startvalue' => '3.25',
            //                 'valueOnRight' => '1',
            //                 'displayvalue' => 'Target Minimum IPK',
            //             ],
            //         ),
            //     ],
            // ),
        );

        $labelAverageIPK = array(
            [
                "category" => []
            ]
        );

        $datasetAverageIPK = array();

        // Pushing labels and values
        array_push(
            $labelAverageIPK[0]['category'],
            [
                "label" => "2.00 - 2.50"
            ],
            [
                "label" => "2.51 - 3.00"
            ],
            [
                "label" => "3.01 - 3.50"
            ],
            [
                "label" => "3.51 - 4.00"
            ],
        );


        foreach ($averageIPKLulusan as $avgIPK) :
            array_push(
                $datasetAverageIPK,
                [
                    "seriesname" => $avgIPK['tahun_lulus'],
                    "data" => array(
                        [
                            "value" => $avgIPK['range_ipk_lulusan_1']
                        ],
                        [
                            "value" => $avgIPK['range_ipk_lulusan_2']
                        ],
                        [
                            "value" => $avgIPK['range_ipk_lulusan_3']
                        ],
                        [
                            "value" => $avgIPK['range_ipk_lulusan_4']
                        ],
                    ),
                ],
            );
        endforeach;

        $configAverageIPK["categories"] = $labelAverageIPK;
        $configAverageIPK["dataset"] = $datasetAverageIPK;

        // JSON Encode the data to retrieve the string containing the JSON representation of the data in the array.
        $jsonEncodedData = json_encode($configAverageIPK);

        // Chart object
        $Chart = new FusionCharts("mscolumn3d", "average_IPK_lulusan_fs_id", "75%", "150%", "average_IPK_lulusan_chart", "json", $jsonEncodedData);

        // Render the chart
        $Chart->render();
        ?>

        <script>
            let firstYear;
            let lastYear;
            let graduateTimeFilter;
            let jenjangFilter;
            let dataForIPKTable = []; // Can do better with DataTables
            var facultyFilter;
            var jurusanFilter;

            updateData = function() {
                firstYear = document.getElementById("filter_ipk_first_year").value;
                lastYear = document.getElementById("filter_ipk_last_year").value;
                jenjangFilter = document.getElementById("filter_jenjang").value;
                graduateTimeFilter = document.querySelector('input[name="filter_graduate_time"]:checked').value;
                facultyFilter = document.getElementById("filter_faculty").value;
                jurusanFilter = document.getElementById("filter_jurusan").value;
                prodiFilter = document.getElementById("filter_prodi").value;


                console.log(jenjangFilter);
                console.log(graduateTimeFilter);

                if (jenjangFilter == 'universitas') {
                    $.ajax({
                        type: 'POST',
                        data: 'firstYear=' + firstYear + '&lastYear=' + lastYear + '&jenjangFilter=' + jenjangFilter + '&graduateTimeFilter=' + graduateTimeFilter,
                        url: 'fetchIPKLulusanFiltered',
                        dataType: 'json',
                        success: function(value) {
                            dataForIPKTable = value.averageipk;
                            $('#table_ipk').replaceWith(buildIPKTable(dataForIPKTable));
                            FusionCharts("average_IPK_lulusan_fs_id").setJSONData(value);
                        }
                    });
                } else if (jenjangFilter == 'fakultas') {
                    $.ajax({
                        type: 'POST',
                        data: 'firstYear=' + firstYear + '&lastYear=' + lastYear + '&jenjangFilter=' + jenjangFilter + '&graduateTimeFilter=' + graduateTimeFilter + '&facultyFilter=' + facultyFilter,
                        url: 'fetchIPKLulusanFiltered',
                        dataType: 'json',
                        success: function(value) {
                            dataForIPKTable = value.averageipk;
                            $('#table_ipk').replaceWith(buildIPKTable(dataForIPKTable));
                            FusionCharts("average_IPK_lulusan_fs_id").setJSONData(value);
                        }
                    });
                } else if (jenjangFilter == 'jurusan') {
                    $.ajax({
                        type: 'POST',
                        data: 'firstYear=' + firstYear + '&lastYear=' + lastYear + '&jenjangFilter=' + jenjangFilter + '&graduateTimeFilter=' + graduateTimeFilter + '&jurusanFilter=' + jurusanFilter,
                        url: 'fetchIPKLulusanFiltered',
                        dataType: 'json',
                        success: function(value) {
                            dataForIPKTable = value.averageipk;
                            $('#table_ipk').replaceWith(buildIPKTable(dataForIPKTable));
                            FusionCharts("average_IPK_lulusan_fs_id").setJSONData(value);
                        }
                    });
                } else {
                    $.ajax({
                        type: 'POST',
                        data: 'firstYear=' + firstYear + '&lastYear=' + lastYear + '&jenjangFilter=' + jenjangFilter + '&graduateTimeFilter=' + graduateTimeFilter + '&prodiFilter=' + prodiFilter,
                        url: 'fetchIPKLulusanFiltered',
                        dataType: 'json',
                        success: function(value) {
                            dataForIPKTable = value.averageipk;
                            $('#table_ipk').replaceWith(buildIPKTable(dataForIPKTable));
                            FusionCharts("average_IPK_lulusan_fs_id").setJSONData(value);
                        }
                    });
                }

                function buildIPKTable(data) {
                    var newRow = '';

                    data.forEach(function(data, index, array) {
                        console.log(data['tahun_lulus']);
                        console.log(data['avg_ipk']);

                        var row = `<tr>
                                    <td>${data['tahun_lulus']}</td>
                                    <td>${data['avg_ipk']}</td>
                                </tr>`

                        newRow += row;
                    });

                    return '<table border="1" id="table_ipk"><thead><th>Tahun Lulus</th><th>Rata-rata IPK Lulusan</th></thead>' + newRow + '</table>';
                }
            }

            $(document).ready(function() {
                $('#filter_jenjang').change(function() {
                    let filterJenjang = $('#filter_jenjang').val();
                    console.log(filterJenjang);

                    if (filterJenjang == 'fakultas') {
                        $('#filter_faculty').off();
                        $('#filter_jurusan').off();
                        $('#filter_prodi').off();
                        $('#filter_ipk_button').prop('disabled', true);

                        console.log('in jenjang ' + filterJenjang);
                        $.ajax({
                            type: 'POST',
                            url: 'fetchFacultyForFilter',
                            success: function(data) {
                                $('#filter_faculty').html(data);
                                $('#label_filter_faculty').prop('hidden', false);
                                $('#filter_faculty').prop('hidden', false);

                                $('#label_filter_jurusan').prop('hidden', true);
                                $('#filter_jurusan').prop('hidden', true);
                                $('#label_filter_jurusan').prop('disabled', true);
                                $('#filter_jurusan').prop('disabled', true);

                                $('#label_filter_prodi').prop('hidden', true);
                                $('#filter_prodi').prop('hidden', true);
                                $('#label_filter_prodi').prop('disabled', true);
                                $('#filter_prodi').prop('disabled', true);
                            }
                        });

                        $('#filter_faculty').change(function() {
                            var filterFaculty = $('#filter_faculty').val();
                            if (filterFaculty != '') {
                                $('#filter_ipk_button').prop('disabled', false);
                                $('input[name="filter_graduate_time"]').prop('hidden', false);
                                $('input[name="filter_graduate_time"]').prop('disabled', false);
                                $('label[name="filter_graduate_time"]').prop('hidden', false);
                            }
                        });

                    } else if (filterJenjang == 'jurusan') {
                        console.log('in jenjang ' + filterJenjang);
                        $('#filter_faculty').off();
                        $('#filter_jurusan').off();
                        $('#filter_prodi').off();
                        $('#filter_ipk_button').prop('disabled', true);
                        $('#filter_jurusan').html('<option value="-">Pilih Jurusan</option>');

                        $.ajax({
                            type: 'POST',
                            url: 'fetchFacultyForFilter',
                            success: function(data) {
                                $('#filter_faculty').html(data);
                                $('#label_filter_faculty').prop('hidden', false);
                                $('#filter_faculty').prop('hidden', false);

                                $('#label_filter_jurusan').prop('hidden', false);
                                $('#filter_jurusan').prop('hidden', false);

                                $('#label_filter_prodi').prop('hidden', true);
                                $('#filter_prodi').prop('hidden', true);
                                $('#label_filter_prodi').prop('disabled', true);
                                $('#filter_prodi').prop('disabled', true);
                            }
                        });

                        $('#filter_faculty').change(function() {
                            var filterFaculty = $('#filter_faculty').val();

                            if (filterFaculty != '') {
                                console.log('in jenjang ' + filterJenjang + 'with non-null filterFaculty');
                                $.ajax({
                                    type: 'POST',
                                    data: 'filterFaculty=' + filterFaculty,
                                    url: 'fetchJurusanForFilter',
                                    success: function(data) {
                                        $('#filter_jurusan').html(data);
                                        $('#label_filter_jurusan').prop('hidden', false);
                                        $('#filter_jurusan').prop('hidden', false);
                                        $('#label_filter_jurusan').prop('disabled', false);
                                        $('#filter_jurusan').prop('disabled', false);

                                        if (jenjangFilter == 'prodi') {
                                            $('#label_filter_jurusan').prop('hidden', false);
                                            $('#filter_jurusan').prop('hidden', false);
                                        }
                                    }
                                })
                            } else {
                                $('#label_filter_jurusan').prop('hidden', true);
                                $('#filter_jurusan').prop('hidden', true);
                                $('#label_filter_jurusan').prop('disabled', true);
                                $('#filter_jurusan').prop('disabled', true);
                                $('#filter_jurusan').html('<option value="-">Pilih Jurusan</option>');

                                $('#label_filter_prodi').prop('hidden', true);
                                $('#filter_prodi').prop('hidden', true);
                                $('#label_filter_prodi').prop('disabled', true);
                                $('#filter_prodi').prop('disabled', true);
                                $('#filter_prodi').html('<option value="-">Pilih Program Studi</option>');
                            }
                        });

                        $('#filter_jurusan').change(function() {
                            var filterJurusan = $('#filter_jurusan').val();
                            if (filterJurusan != '') {
                                $('#filter_ipk_button').prop('disabled', false);
                                $('input[name="filter_graduate_time"]').prop('hidden', false);
                                $('input[name="filter_graduate_time"]').prop('disabled', false);
                                $('label[name="filter_graduate_time"]').prop('hidden', false);
                            }
                        });

                    } else if (filterJenjang == 'prodi') {
                        console.log('in jenjang ' + filterJenjang);
                        $('#filter_faculty').off();
                        $('#filter_jurusan').off();
                        $('#filter_prodi').off();
                        $('#filter_ipk_button').prop('disabled', true);
                        $('#filter_prodi').html('<option value="-">Pilih Program Studi</option>');

                        $.ajax({
                            type: 'POST',
                            url: 'fetchFacultyForFilter',
                            success: function(data) {
                                $('#filter_faculty').html(data);
                                $('#label_filter_faculty').prop('hidden', false);
                                $('#filter_faculty').prop('hidden', false);

                                $('#label_filter_jurusan').prop('hidden', false);
                                $('#filter_jurusan').prop('hidden', false);
                                $('#filter_jurusan').prop('disabled', true);
                                $('#filter_jurusan').html('<option value="-">Pilih Jurusan</option>');

                                $('#label_filter_prodi').prop('hidden', false);
                                $('#filter_prodi').prop('hidden', false);
                            }
                        });

                        $('#filter_faculty').change(function() {
                            var filterFaculty = $('#filter_faculty').val();

                            if (filterFaculty != '') {
                                console.log('in jenjang ' + filterJenjang + 'with non-null filterFaculty');
                                $.ajax({
                                    type: 'POST',
                                    data: 'filterFaculty=' + filterFaculty,
                                    url: 'fetchJurusanForFilter',
                                    success: function(data) {
                                        $('#filter_jurusan').html(data);
                                        $('#filter_jurusan').prop('disabled', false);
                                        $('#filter_prodi').html('<option value="-">Pilih Program Studi</option>');
                                        $('#filter_prodi').prop('disabled', true);
                                        $('#filter_ipk_button').prop('disabled', true);
                                    }
                                })
                            } else {
                                $('#label_filter_jurusan').prop('hidden', true);
                                $('#filter_jurusan').prop('hidden', true);
                                $('#label_filter_jurusan').prop('disabled', true);
                                $('#filter_jurusan').prop('disabled', true);
                                $('#filter_jurusan').html('<option value="-">Pilih Jurusan</option>');

                                $('#label_filter_prodi').prop('hidden', true);
                                $('#filter_prodi').prop('hidden', true);
                                $('#filter_prodi').prop('disabled', true);
                                $('#filter_prodi').html('<option value="-">Pilih Program Studi</option>');
                            }
                        });

                        $('#filter_jurusan').change(function() {
                            var filterJurusan = $('#filter_jurusan').val();

                            if (filterJurusan != '') {
                                console.log('in jenjang ' + filterJenjang + 'with non-null filterJurusan');

                                $.ajax({
                                    type: 'POST',
                                    data: 'filterJurusan=' + filterJurusan,
                                    url: 'fetchProdiForFilter',
                                    success: function(data) {
                                        $('#filter_prodi').html(data);
                                        $('#label_filter_prodi').prop('hidden', false);
                                        $('#filter_prodi').prop('disabled', false);
                                        $('#filter_ipk_button').prop('disabled', true);
                                    }
                                });

                            } else {
                                $('#label_filter_jurusan').prop('hidden', true);
                                $('#filter_jurusan').prop('hidden', true);
                                $('#label_filter_jurusan').prop('disabled', true);
                                $('#filter_jurusan').prop('disabled', true);
                                $('#filter_jurusan').html('<option value="-">Pilih Jurusan</option>');

                                $('#label_filter_prodi').prop('hidden', true);
                                $('#filter_prodi').prop('hidden', true);
                                $('#label_filter_prodi').prop('disabled', true);
                                $('#filter_prodi').prop('disabled', true);
                                $('#filter_prodi').html('<option value="-">Pilih Program Studi</option>');
                            }
                        });

                        $('#filter_prodi').change(function() {
                            var filterProdi = $('#filter_prodi').val();
                            if (filterProdi != '') {
                                $('#filter_ipk_button').prop('disabled', false);
                                $('input[name="filter_graduate_time"]').prop('hidden', false);
                                $('input[name="filter_graduate_time"]').prop('disabled', false);
                                $('label[name="filter_graduate_time"]').prop('hidden', false);
                            }
                        });

                    } else {
                        $('#filter_ipk_button').prop('disabled', false);

                        $('#filter_faculty').html('<option value="-">Pilih Fakultas</option>');
                        $('#label_filter_faculty').prop('hidden', true);
                        $('#filter_faculty').prop('hidden', true);

                        $('#filter_jurusan').html('<option value="-">Pilih Jurusan</option>');
                        $('#label_filter_jurusan').prop('hidden', true);
                        $('#filter_jurusan').prop('hidden', true);
                        $('#label_filter_jurusan').prop('disabled', true);
                        $('#filter_jurusan').prop('disabled', true);

                        $('#filter_prodi').html('<option value="-">Pilih Program Studi</option>');
                        $('#label_filter_prodi').prop('hidden', true);
                        $('#filter_prodi').prop('hidden', true);
                        $('#label_filter_prodi').prop('disabled', true);
                        $('#filter_prodi').prop('disabled', true);
                    }
                });
            });
        </script>
    </section>

</body>

</html>
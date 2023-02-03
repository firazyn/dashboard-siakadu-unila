<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->testModel = new TestModel();
	}

	public function index()
	{
		$data = [
			'nilaiSkripsi' => $this->testModel->getNilaiSkripsiData(),
			'averageIPKLulusan' => $this->testModel->getAverageIPKData(null, null, null, null),
			'graduateYear' => $this->testModel->getGraduateYear(),
		];
		// echo json_encode($data['averageIPKLulusan']);
		$this->load->view('test_page', $data);
	}
	
	public function test()
	{
		$this->load->view('welcome_message');
	}

	public function fetchIPKLulusanFiltered()
	{
		$firstYear = $this->input->post('firstYear');
		$lastYear = $this->input->post('lastYear');
		$jenjangFilter = $this->input->post('jenjangFilter');
		$graduateTimeFilter = $this->input->post('graduateTimeFilter');

		if ($jenjangFilter == 'fakultas') {
			$unitfilter = $this->input->post('facultyFilter');
		} else if ($jenjangFilter == 'jurusan') {
			$unitfilter = $this->input->post('jurusanFilter');
		} else if ($jenjangFilter == 'prodi') {
			$unitfilter = $this->input->post('prodiFilter');
		} else {
			$unitfilter = null;
		}

		if($firstYear != '-' && $lastYear != '-') {
			if ($firstYear > $lastYear) {
				$tempYear = 2008;
				$tempYear = $lastYear;
				$lastYear = $firstYear;
				$firstYear = $tempYear;
			}

			$yearInterval = "Per Tahun, Interval $firstYear - $lastYear";
		} else if ($firstYear == '-' && $lastYear == '-') {
			$yearInterval = "Per Tahun";
		} else if ($firstYear == '-') {
			$yearInterval = "Per Tahun, Sebelum Tahun $lastYear";
		} else if ($lastYear == '-') {
			$yearInterval = "Per Tahun, Setelah Tahun $firstYear";
		} else {
			$yearInterval = "Per Tahun";
		}

		$averageIPKLulusan = $this->testModel->getAverageIPKData($unitfilter, $graduateTimeFilter, $firstYear, $lastYear);
		


		$configAverageIPK = array(
			"chart" => array(
				"caption" => "Column Chart Rata-rata IPK Lulusan",
				"subCaption" => $yearInterval,
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

		$dataForIPKTable = array();

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

		foreach ($averageIPKLulusan as $avgIPK) :
			array_push(
				$dataForIPKTable,
				[
					"tahun_lulus" => $avgIPK['tahun_lulus'],
					"avg_ipk" => $avgIPK['ipk_lulusan'],
				],
			);
		endforeach;

		$configAverageIPK["categories"] = $labelAverageIPK;
		$configAverageIPK["dataset"] = $datasetAverageIPK;
		$configAverageIPK["averageipk"] = $dataForIPKTable;


		echo json_encode($configAverageIPK);
	}

	public function fetchFacultyForFilter()
	{
		$facultiesNames = $this->testModel->getFacultiesNames();

		$output = '<option selected="true" disabled>Pilih Fakultas</option>';

		foreach ($facultiesNames as $fn) {

			$output .= '<option value="' . $fn['idunit'] . '">' . $fn['namaunit'] . '</option>';
		}

		echo $output;
	}

	public function fetchJurusanForFilter()
	{
		$filterFaculty = $this->input->post('filterFaculty');
		$jurusanNames = $this->testModel->getJurusanNames($filterFaculty);

		$output = '<option selected="true" disabled>Pilih Jurusan</option>';

		foreach ($jurusanNames as $jn) {

			$output .= '<option value="' . $jn['idunit'] . '">' . $jn['namaunit'] . '</option>';
		}

		echo $output;
	}

	public function fetchProdiForFilter()
	{
		$filterJurusan = $this->input->post('filterJurusan');
		$prodiNames = $this->testModel->getProdiNames($filterJurusan);

		$output = '<option selected="true" disabled>Pilih Program Studi</option>';

		foreach ($prodiNames as $pn) {

			$output .= '<option value="' . $pn['idunit'] . '">' . $pn['namaunit'] . '</option>';
		}

		echo $output;
	}
}

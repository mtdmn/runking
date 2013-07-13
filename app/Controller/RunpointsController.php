<?php
include_once('GpxParser.php');

class RunpointsController extends AppController {
	public $helpers = array('Html', 'Form', 'Session');
	public $components = array('Session');

	public function index() {
		$this->set('runpoints', $this->Runpoint->find('all'));
	}

	public function view($id) {
		$this->Runpoint->id = $id;
		$this->set('runpoint', $this->Runpoint->read());

	}

	public function map() {
		$points = $this->Runpoint->find('all');
		foreach ($points as $p) {
			if (preg_match('/\((\d+\.?\d*) (\d+\.?\d*)\)/', $p['Runpoint']['latlngtxt'], $matches)) {
				$np[] = array('Y' => $matches[1], 'X' => $matches[2]);
			} else {
				print_r($p);
			}
		}
        $this->set('runpoints', $np);
		$this->layout = 'openlayer';
	}

	public function upload() {
		if ($this->request->is('post')) {
			$tmp = $this->request->data['Runpoint']['GPX']['tmp_name'];
			// check file upload error.
			if ($this->request->data['Runpoint']['GPX']['error']!=0) {
				$this->Session->setFlash('File upload failed:'.
					$this->request->data['Runpoint']['GPX']['error']);
					break;
			}

			if(is_uploaded_file($tmp)) {
				$value = file_get_contents($tmp);
				// retrieve runpoints extracted from the GPX file.
				$gpxp = new GpxParser($value, 'gpx');
				$points = $gpxp->getRunpoints();
				// save these runpoints to the DB.
				foreach($points as $wkt) {
					$this->Runpoint->create();
					$this->Runpoint->replaceinto(
						array(
							'create_timestamp'=>"NOW()",
							'latlng'=>'POINT('.$wkt.')'
							)
						);
				}
			}
			$this->Session->setFlash('GPX is uploaded.');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash('Post your GPX file.');
		}
	}

}
?>

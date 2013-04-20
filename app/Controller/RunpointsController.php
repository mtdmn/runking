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
				$gpxp = new GpxParser($value);
				$points = $gpxp->getRunpoints();
				// save these runpoints to the DB.
				foreach($points as $wkt) {
					$this->Runpoint->create();
					$this->Runpoint->set(
						array(
							'create_timestamp'=>DboSource::expression("NOW()"),
//							'latlng'=>DboSource::expression('PointFromText("'.$wkt.'")')
							'latlng'=>'POINT('.$wkt.')'
							)
						);
					$this->Runpoint->save();
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

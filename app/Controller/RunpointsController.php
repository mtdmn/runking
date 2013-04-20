<?php
include_once('geoPHP/geoPHP.inc');

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
			$tmp = $this->request->data['Upload']['file']['tmp_name'];
			if(is_uploaded_file($tmp)) {
				// check if the file is in GPX format. XXX
				// retrieve runpoints extracted from the GPX file.
				// save these runpoints to the DB.
/*
				$file_name = basename($this->request->data['Upload']['file']['name']);
				$file = WWW_ROOT.'files'.DS.$file_name;
				if (move_uploaded_file($tmp, $file)) {
					$this->Upload->create();
					$this->request->data['Upload']['file_name'] = $file_name;
					if ($this->Upload->save($this->request->data)) {
						$this->Session->setFlash(__('The upload has been saved'));
						$this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash(__('The upload could not be saved. Please, try again.'));
					}
				}
*/
			}
			$this->Session->setFlash('Something is posted.');
//                $this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash('Post your GPX file.');
		}
	}

	public function upload_test() {
		$wktarray = array(
			'POINT(35.668 139.537)',
			'POINT(35.669 139.537)',
			'POINT(35.67 139.537)',
			'POINT(35.667 139.538)',
			'POINT(35.668 139.538)',
			'POINT(35.669 139.538)'
		);
		foreach($wktarray as $wkt) {
			$this->Runpoint->create();
			$this->Runpoint->set(
				array(
					'create_timestamp'=>DboSource::expression("NOW()"),
//					'latlng'=>DboSource::expression('PointFromText("'.$wkt.'")')
					'latlng'=>$wkt
					)
				);
			$this->Runpoint->save();
		}

		$this->Session->setFlash('upload completed.');
		$this->redirect(array('action' => 'index'));
	}

    public function add() {
        if ($this->request->is('post')) {
            if ($this->Runpoint->save($this->request->data)) {
                $this->Session->setFlash('Your Runpoint has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add your point.');
            }
        }
    }

	public function edit($id = null) {
   	 $this->Runpoint->id = $id;
   	 if ($this->request->is('get')) {
   	     $this->request->data = $this->Runpoint->read();
   	 } else {
   	     if ($this->Runpoint->save($this->request->data)) {
   	         $this->Session->setFlash('Your Runpoint has been updated.');
   	         $this->redirect(array('action' => 'index'));
   	     } else {
   	         $this->Session->setFlash('Unable to update your Runpoint.');
   	     }
   	 }
	}

	public function delete($id) {
   	 if ($this->request->is('get')) {
    	    throw new MethodNotAllowedException();
    	}
    	if ($this->Runpoint->delete($id)) {
        	$this->Session->setFlash('The Runpoint with id: ' . $id . ' has been deleted.');
        	$this->redirect(array('action' => 'index'));
    	}
	}
}
?>

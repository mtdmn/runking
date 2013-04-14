<?php
include_once('geoPHP/geoPHP.inc');

class RunpointsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session');

    public function index() {
        $this->set('runpoints', $this->Runpoint->find('all'));
//        $this->set('points', $this->Point->find('textpoint',array('fields'=>$fields)));
//        $this->set('points', $this->Point->find('textpoint'));
    }

    public function view($id) {
        $this->Runpoint->id = $id;
        $this->set('runpoint', $this->Runpoint->read());

    }

	public function upload() {
		$wktarray = array(
			'POINT(35.668 139.537)',
			'POINT(35.669 139.537)',
			'POINT(35.67 139.537)',
			'POINT(35.667 139.538)',
			'POINT(35.668 139.538)',
			'POINT(35.669 139.538)'
		);
		foreach($wktarray as $wkt) {
			$this->Runpoint->save($wkt);
		}

		$this->Session->setFlash('upload completed.');
		$this->redirect(array('action' => 'index'));
	}

    public function add() {
        if ($this->request->is('point')) {
            if ($this->Point->save($this->request->data)) {
                $this->Session->setFlash('Your point has been saved.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add your point.');
            }
        }
    }

	public function edit($id = null) {
   	 $this->Point->id = $id;
   	 if ($this->request->is('get')) {
   	     $this->request->data = $this->Point->read();
   	 } else {
   	     if ($this->Point->save($this->request->data)) {
   	         $this->Session->setFlash('Your point has been updated.');
   	         $this->redirect(array('action' => 'index'));
   	     } else {
   	         $this->Session->setFlash('Unable to update your point.');
   	     }
   	 }
	}

	public function delete($id) {
   	 if ($this->request->is('get')) {
    	    throw new MethodNotAllowedException();
    	}
    	if ($this->Point->delete($id)) {
        	$this->Session->setFlash('The point with id: ' . $id . ' has been deleted.');
        	$this->redirect(array('action' => 'index'));
    	}
	}
}
?>

<?php

class WorkoutsController extends AppController {
	public function index() {
		$this->set('workouts', $this->Workout->find('all'));
	}
}

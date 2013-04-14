<?php
class Post extends AppModel {
    public $validate = array(
        'title' => array(
				'alphaNumeric' => array(
                'rule'     => 'alphaNumeric',
                'required' => true,
                'message'  => 'Alphabets and numbers only'
            ),
        ),
        'body' => array(
            'rule' => 'notEmpty'
        )
    );
}
?>

<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Debugger.php';
require_once 'Form.php';

$db = new Debugger();

$form = new Form('test');
$form->menu = new Input('uniqueChoice', 'menu', [
    'rendering' => 'menu',
    'label' => 'Your options',
    'values' => [
        ['name' => 'opt1', 'label' => 'Option 1'],
        ['name' => 'opt2', 'label' => 'Option 2'],
        ['name' => 'opt3', 'label' => 'Option 3'],
    ]
        ]);
$form->file = new Input('file', 'attachment', [
    'maxFileSize' => 4000,
    'label' => 'Coucou your file',
    'labelClass' => 'left',
    'validations' => [
        ['type' => 'required', 'message' => 'C\'est necessaire d\'ajouter un fichier']
    ]
        ]);
$form->linkInputs();
?>

<html>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
    <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js" type="text/javascript"></script>
    <!--
      <script type='text/javascript'>
          $(function () {
              $(".sortable").sortable();
              $(".sortable").disableSelection();
          });
      </script>
    -->
    <style>
        form > fieldset > label {color:blue;}
        form > fieldset > label.error {color:red;}
    </style>
    <?php $form->displayValidation(1); ?>
    <body>
        <div> Post (if set) :
            <?php
            if ($form->isCompleted()) {
                $form->fromPost();
                $db->dump($form->menu->value);
                $db->dump($_POST);
            }
            ?>
        </div>


        <div>
            <?php
            $form->display(3);
            ?>
        </div>
    </body>
</html>

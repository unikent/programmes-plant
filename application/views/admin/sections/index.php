
                <?php echo View::make('admin.inc.meta')->render()?>
    <title>Programmes Plant</title>
  </head>
  <body>
    <?php echo View::make('admin.inc.header')->render()?>
    <div class="container">

      <div class="row-fluid">

        <div class="span3"> <!-- Sidebar -->
          <div class="well">
            <?php echo View::make('admin.inc.sidebar')->render()?>
          </div>
        </div> <!-- /Sidebar -->

        <div class="span9">
          <h1>Sections</h1>
          <p>Use the table below to edit the sections available in this system.</p>
          <?php echo Messages::get_html()?>
          <?php
            if($sections){
              echo '<table class="table table-striped table-bordered table-condensed">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody class="sortable-sections-tbody">';
              foreach($sections as $section){

                echo '<tr id="field-id-'.$section->id.'">
                  <td><i class="icon-move"></i> '.$section->name.'</td>
                  <td><a class="btn btn-primary" href="'.action('/sections@edit', array($section->id)).'">Edit</a> <a class="delete_toggler btn btn-danger" rel="'.$section->id.'">Delete</a></td>
                </tr>';
              }
              echo '</tbody></table>';
            }else{
              echo '<div class="well"><p>There are no sections in the system yet. Feel free to add one below.</p></div>';
            }
          ?>


           <div class="form-actions">
          <a href="<?php echo action('/sections@create')?>" class="btn btn-primary right">New section</a>
        </div>
        </div>

      </div>

    </div> <!-- /container -->
    <div class="modal hide fade" id="delete_section">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('modals.confirm_title'); ?></h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this section?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open('/sections/delete', 'POST')?>
        <a data-dismiss="modal" href="#delete_section" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Delete" />
        <?php echo Form::close()?>
      </div>
    </div>
    
    <script>
      $('#delete_section').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.delete_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue').attr('value',$(elem).attr('rel'));
            $('#delete_section').modal('show');
          });
      });
    </script>
  </body>
</html>

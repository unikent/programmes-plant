<?php echo View::make('admin.inc.meta')->render()?>
    <title>Courses Dashboard</title>
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

        <div class="span9 crud">
          <h1><?php echo ( $create ? 'New Leaflet' : 'Edit Leaflet' )?></h1>
          <?php echo Messages::get_html()?>
          <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/leaflets/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>
            
            <?php if(!$create): ?> <input type="hidden" name="id" value="<?php echo $leaflet->id?>" /> <?php endif; ?>
             
            <fieldset>
              <legend>Leaflet Details</legend>

              <div class="control-group">
                <?php echo Form::label('name', 'Name', array('class'=>'control-label'))?>
                <div class="controls">
                  <?php echo Form::text('name',  ( Input::old('name') || $create ? Input::old('name') : $leaflet->name ),array('placeholder'=>'Enter Leaflet Name...'))?>
                </div>
              </div>

              <div class="control-group">
                <?php echo Form::label('campus', 'Campus', array('class'=>'control-label'))?>
                <div class="controls">
                  <?php echo Form::select('campus', Campus::getAsList(), ($create ? "" : $leaflet->campuses_id ))?>
                </div>
              </div>

              <div class="control-group">
                <?php echo Form::label('tracking_code', 'Tracking code', array('class'=>'control-label'))?>
                <div class="controls">
                  <?php echo Form::text('tracking_code',  ( Input::old('tracking_code') || $create ? Input::old('tracking_code') : $leaflet->tracking_code ),array('placeholder'=>'Enter Tracking code...'))?>
                </div>
              </div>

            </fieldset>
            <div class="form-actions">
              <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/leaflets')?>">Go Back</a>
              <input type="submit" class="btn btn-primary" value="<?php echo ($create ? 'Create Leaflet' : 'Save Leaflet')?>" />
            </div>

          <?php echo Form::close()?>
        </div>
      </div>

    </div> <!-- /container -->
    <div class="modal hide fade" id="delete_leaflet">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this leaflet?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open('leaflet/delete', 'POST')?>
        <a data-toggle="modal" href="#delete_leaflet" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Delete" />
        <?php echo Form::close()?>
      </div>
    </div>
    <?php echo View::make('admin.inc.scripts')->render()?>
    <script>
      $('#delete_leaflet').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.delete_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue').attr('value',$(elem).attr('rel'));
            $('#delete_leaflet').modal('show');
          });
      });
    </script>
  </body>
</html>

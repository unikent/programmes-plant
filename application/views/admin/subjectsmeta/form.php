<?php echo View::make('admin.inc.meta')->render();

$Short_url = URI::segment(1).'/'.URI::segment(2) .'/meta/'.$meta_type.'s';

?>
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
          <h1>Create new field</h1>
          <?php echo Messages::get_html()?>
          <?php echo Form::open_for_files($Short_url.'/add', 'POST', array('class'=>'form-horizontal'));?>

           <script type='text/javascript'>
               function tryShow(x){
                  if(x.value=="select"){document.getElementById("ext_opt").style.display="block";}
                  else{document.getElementById("ext_opt").style.display="none";}
               }
            </script>


          <fieldset>
            <legend>Basic Information</legend>
            
            <div class="control-group">
              <?php echo Form::label('title', "Title",array('class'=>'control-label'))?>
              <div class="controls">
               
                <?php echo  Form::text('title', (isset($values)) ? $values->field_name : '' )?>
              </div>
            </div>
            <div class="control-group">
              <?php echo Form::label('type', "Type",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo  Form::select('type', array('text'=>'text','textarea'=>'textarea','select'=>'select', 'checkbox'=>'checkbox'), (isset($values)) ? $values->field_type : '', array('onchange'=>'tryShow(this);') )?>
              </div>
            </div>

            <div class="control-group" id='ext_opt' <?php if(isset($values) && $values->field_type=='select'){}else{ echo 'style="display:none;"'; }?>>
              <?php echo Form::label('options', "Options",array('class'=>'control-label'))?>
              <div class="controls">
                <?php echo  Form::text('options', (isset($values)) ? $values->field_meta : '' )?><br/>
                List options as a comma seperated list.
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('year', "Description/Hints",array('class'=>'control-label'))?>
              <div class="controls">
               
                <?php echo  Form::textarea('description', (isset($values)) ? $values->field_description : '' )?>
              </div>
            </div>
 
            <div class="control-group">
              <?php echo Form::label('initval', "Start value",array('class'=>'control-label'))?>
              <div class="controls">
               
                <?php echo  Form::text('initval', (isset($values)) ? $values->field_initval  : '')?> <?php echo Form::checkbox('prefill','1', (isset($values)) ? ($values->prefill==1) ? 1 : 0 : false)?> (Pre fill forms?)<br/>
               <?php  if(!isset($values)): ?> Warning, this value will be applied to all existing records (except if the datatype is textarea). <?php endif; ?>   
              </div>
            </div>

            <div class="control-group">
              <?php echo Form::label('placeholder', "Place holder text",array('class'=>'control-label'))?>
              <div class="controls">
               
                <?php echo  Form::text('placeholder', (isset($values)) ? $values->placeholder : '' )?>
              </div>
            </div>

  <?php  if(isset($values)): ?> 
       <?php echo  Form::hidden('id', $values->id)?><br/>
   <?php endif; ?>         
           
</fieldset>

    
          <div class="form-actions">
            <a class="btn" href="<?php echo url($Short_url.'/index')?>">Go Back</a>
            <input type="submit" class="btn btn-primary" value="Save" />
          </div>

        </div>

      </div>

    </div> <!-- /container -->

    <?php echo View::make('admin.inc.scripts')->render()?>

  </body>
</html>

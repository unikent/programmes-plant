<?php if (! $create)  : ?>
  <?php $award_name = URLParams::$type == 'pg' ? $programme->get_award_names() : $programme->award->name?>
  <?php echo View::make('admin.revisions.partials.revision_header', array('revision' => $active_revision, 'instance' => $programme, 'type'=>'programmes'))->render(); ?>
<?php endif; ?>

<?php echo Form::open_for_files(URI::segment(1).'/'.URI::segment(2).'/programmes/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>

<div class="floating_save" data-spy="affix" data-offset-top="100">
   <div class='pull-right'>
    <input type="submit" class="btn btn-warning" value="Save">
    <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/programmes')?>">Cancel</a>
  </div>
  <?php echo View::make('admin.inc.partials.type_marker')->render(); ?>
  <strong><?php echo ( $create ? __('programmes.create_programme_title') : $programme->$title_field ); ?> <?php echo isset($award_name) ? ' - <em>'.$award_name.'</em>' : '' ; ?></strong>
</div>


<?php echo Messages::get_html()?>

<h1><?php echo View::make('admin.inc.partials.type_marker')->render(); ?><?php echo ( $create ? __('programmes.create_programme_title') : $programme->$title_field ); ?><?php echo isset($award_name) ? ' - <em>'.$award_name.'</em>' : '' ; ?></h1>

<p><?php echo ( $create ? __('programmes.create_introduction') : __('programmes.edit_introduction') ); ?></p>


<?php if (! $create): ?>
  <?php echo  Form::hidden('programme_id', $programme->id); ?>
<?php endif; ?>

<div class="control-group">
  <?php echo Form::label('year', "Year",array('class'=>'control-label'))?>
  <div class="controls">
      <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $programme->year )?></span>
      <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $programme->year ), array('class'=>'uneditable-input'))?>
  </div>
</div>

<?php echo View::make('admin.inc.partials.formfields', array('year' => $year, 'model' => $model, 'sections' => $sections, 'programme' => isset($programme) ? $programme : null,'create'=>($create && !$clone), 'from' => 'programmes'))->render(); ?>
<?php if($programme->exists){ ?>
<div class="section accordion accordion-group">
  <div class="accordion-heading">
    <legend>
      <a href="#feedata" class="accordion-toggle" data-toggle="collapse">Fee data</a>
    </legend>
  </div>
  <div id="feedata" class="accordion-body collapse in">
    <div class="control-group" style='padding:10px;'>
    <?php if (! $create): ?>

    <?php

        $feesets = array();
        $displayed = array();

        foreach($programme->get_deliveries() as $del){
          // ignore duplicates
          if(in_array($del->pos_code, $displayed)) continue;
          // add to output
           $feesets[] =  array('pos' => $del->pos_code, 'year'=> $programme->year);
           $displayed[] = $del->pos_code;
        }
    
        // Show info
        echo View::make('admin.inc.partials.feeblock', array('feesets' => $feesets))->render();

        ?>
    <?php endif; ?>

    </div>
  </div>
</div>
<br/>

<div class="section accordion accordion-group">
  <div class="accordion-heading">
    <legend>
      <a href="#deliveries" class="accordion-toggle" data-toggle="collapse">Programme Deliveries</a>
    </legend>
  </div>
  <div id="deliveries" class="accordion-body collapse in">
    <div class="control-group">
      <iframe src="../deliveries/<?php echo $programme->id ?>" style='width:100%; height:600px; border:0;'></iframe>
    </div>
  </div>
</div>
<?php } ?>
<?php if (Auth::user()->can('isSuperDuperUser') && $programme->exists) { ?>
<div class="section accordion accordion-group">
    <div class="accordion-heading">
        <legend>
            <a href="#notes" class="accordion-toggle" data-toggle="collapse">Notes</a>
        </legend>
    </div>
    <div id="notes" class="accordion-body collapse in">
        <div class="control-group">
            <label for="note" class="control-label">Notes</label>
            <div class="controls">
                <textarea id="note"><?php echo $notes->note; ?></textarea>
                <span class="help-block">For Internal use only. This field will NOT be displayed publicly anywhere.</span>
            </div>
        </div>
        <div class="control-group">
            <label for="short_note" class="control-label">Short Note</label>
            <div class="controls">
                <input type="text" id="short_note" value="<?php echo $notes->short_note; ?>">
                <span class="help-block">For Internal use only. This field will NOT be displayed publicly anywhere.</span>
            </div>
        </div>
        <div class="notes-form-actions">
            <p class="alert alert-success" id="notes_success" style="display: none;">Notes Updated</p>
            <p class="alert alert-danger" id="notes_fail" style="display: none;">Error: failed to save Notes. Please try again.</p>
            <span id="submit_notes" class="btn btn-warning">Save Notes</span>
        </div>
    </div>
</div>
<?php } ?>
<?php echo Form::actions('programmes', isset($programme) ? $programme : null) ?>

<?php echo Form::close(); ?>


<a href='#' class='scroll-to-top'><i class="icon-chevron-up icon-white"></i></a>
<?php if (Auth::user()->can('isSuperDuperUser') && $programme->exists) { ?>
<form id="notesform" method="POST" action="<?php echo $notes->exists?URL::to('notes/update'):URL::to('notes/create');?>">
    <?php if($notes->exists){
    ?>
        <input type="hidden" name="id" value="<?php echo $notes->id; ?>">
    <?php
    }?>
    <?php echo Form::token();?>
    <input type="hidden" name="programme_id" value="<?php echo $programme->id; ?>">
    <input type="hidden" id="actual_note" name="note" value="<?php echo $notes->note; ?>">
    <input type="hidden" id="actual_short_note" name="short_note" value="<?php echo $notes->short_note; ?>">
</form>
<?php } ?>

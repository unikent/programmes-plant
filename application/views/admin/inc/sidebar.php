<div class="navwell">
  <ul class="nav nav-list">
        
        <li class="nav-header">Core Data</li>

         <?php if (Auth::user()->can(array("edit_own_programmes", "view_all_programmes", "edit_all_programmes"))): ?>

        <li class="<?php echo ( (URI::segment(2) != 'fields' && URI::segment(3) == 'programmes') ? 'active' : false )?>"><a href="<?php echo url(URLParams::$mainpath.'programmes')?>"><i class="icon-home"></i> Programmes</a></li>
       
        <?php endif; ?>

        <?php if (Auth::user()->can("recieve_edit_requests")): ?>
            <li class="nav-header">Editing</li>
            <li class="<?php echo ( (URI::segment(2) == 'inbox' ) ? 'active' : false )?>"><a href="<?php echo url('editor/inbox')?>"><i class="icon-inbox"></i> Inbox</a></li>
        <?php endif; ?>
        
        <?php if (Auth::user()->can("edit_data")): ?>
            <li class="nav-header">Data</li>
            <li class="<?php echo ( URI::segment(1) == 'awards' ? 'active' : false )?>"><a href="<?php echo url('awards')?>"><i class="icon-list-alt"></i> Awards</a></li>
            <li class="<?php echo ( URI::segment(1) == 'campuses' ? 'active' : false )?>"><a href="<?php echo url('campuses')?>"><i class="icon-list-alt"></i> Campuses</a></li>
            <li class="<?php echo ( URI::segment(1) == 'faculties' ? 'active' : false )?>"><a href="<?php echo url('faculties')?>"><i class="icon-list-alt"></i> Faculties</a></li>
            <li class="<?php echo ( URI::segment(1) == 'leaflets' ? 'active' : false )?>"><a href="<?php echo url('leaflets')?>"><i class="icon-list-alt"></i> Leaflets</a></li>
            <li class="<?php echo ( URI::segment(1) == 'schools' ? 'active' : false )?>"><a href="<?php echo url('schools')?>"><i class="icon-list-alt"></i> Schools</a></li>
            <li class="<?php echo ( URI::segment(1) == 'subjects' ? 'active' : false )?>"><a href="<?php echo url('subjects')?>"><i class="icon-list-alt"></i> Subjects</a></li>
            <li class="<?php echo ( URI::segment(1) == 'subjectcategories' ? 'active' : false )?>"><a href="<?php echo url('subjectcategories')?>"><i class="icon-list-alt"></i> Subject categories</a></li>
        <?php endif; ?>

        <?php if (Auth::user()->can("edit_immutable_data")): ?>
            <li class="<?php echo ( (URI::segment(1) != 'fields' && URI::segment(2) == 'globalsettings') ? 'active' : false )?>"><a href="<?php echo url(URLParams::$year.'/globalsettings')?>"><i class="icon-list-alt"></i> Immutable fields</a></li>
        <?php endif; ?>

        <?php if (Auth::user()->can("edit_overridable_data")): ?>
            <li class="<?php echo ( (URI::segment(2) != 'fields' && URI::segment(3) == 'programmesettings') ? 'active' : false )?>"><a href="<?php echo url(URLParams::$mainpath.'programmesettings')?>"><i class="icon-list-alt"></i> Overridable fields</a></li>  
        <?php endif; ?>

        <?php if (Auth::user()->can("configure_fields")): ?>
            <li class="nav-header">Field setup</li>

            <li class="<?php echo ( (URI::segment(1) == 'fields' && URI::segment(2) == 'immutable') ? 'active' : false )?>"><a href="<?php echo url('fields/immutable')?>"><i class="icon-cog"></i> Immutable fields </a></li>

            <li class="<?php echo ( (URI::segment(2) == 'fields' && URI::segment(3) == 'standard') ? 'active' : false )?>"><a href="<?php echo url(URLParams::$type.'/fields/standard')?>"><i class="icon-cog"></i> Programme fields</a></li>
        <?php endif; ?>

        <?php if (Auth::user()->can("manage_users")): ?>
            <li class="nav-header">User managment</li>
            <li class="<?php echo ( (URI::segment(1) == 'users' ) ? 'active' : false )?>"><a href="<?php echo url('users')?>"><i class="icon-user"></i> Users </a></li>
        <?php endif; ?>


</ul>
</div>

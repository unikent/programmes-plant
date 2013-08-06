      <?php foreach ($programmes as $programme): ?>
        <course>
          <dc:description>
            <div xmlns="http://www.w3.org/1999/xhtml">
              <![CDATA[<?php echo (strip_tags($programme['programme_overview_text'])); ?>]]>
            </div>
          </dc:description>
          <dc:identifier><![CDATA[<?php echo ($programme['url']); ?>]]></dc:identifier>
          <?php if (isset($programme['subjects'])): ?>
            <?php foreach ($programme['subjects'] as $subject): ?>
              <?php if (!empty($subject)): ?>
                <dc:subject><![CDATA[<?php echo ($subject['name']) ?>]]></dc:subject>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
          <dc:title><![CDATA[<?php echo ($programme['programme_title']); ?>]]></dc:title>
          <dc:type><?php echo __("programmes.{$programme['type']}"); ?></dc:type>
          <mlo:url><?php echo ($programme['url']); ?></mlo:url>
          <?php if (isset($programme['programme_abstract'])): ?>
            <abstract><![CDATA[<?php echo (strip_tags($programme['programme_abstract'])); ?>]]></abstract>
          <?php endif; ?>
          <?php if(!empty($programme['how_to_apply'])): ?>
            <applicationProcedure>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <![CDATA[<?php echo ($programme['how_to_apply']); ?>]]>
              </div>
            </applicationProcedure>
          <?php endif; ?>
          <?php if(!empty($programme['teaching_and_assessment'])): ?>
            <mlo:assessment>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <![CDATA[<?php echo ($programme['teaching_and_assessment']); ?>]]>
              </div>
            </mlo:assessment>
          <?php endif; ?>
          <?php if (isset($programme['learning_outcomes'])): ?>
            <learningOutcome>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <![CDATA[
                  <p><strong>Knowledge and understanding</strong></p>
                  <?php echo ($programme['learning_outcomes']); ?>

                <?php if (isset($programme['intellectual_skills_learning_outcomes'])): ?>
                  <p><strong>Intellectual Skills</strong></p>
                  <?php echo ($programme['intellectual_skills_learning_outcomes']); ?>
                <?php endif; ?>

                <?php if (isset($programme['subjectspecific_skills_learning_outcomes'])): ?>
                  <p><strong>Subject-specific skills</strong></p>
                  <?php echo ($programme['subjectspecific_skills_learning_outcomes']); ?>
                <?php endif; ?>

                <?php if (isset($programme['transferable_skills_learning_outcomes'])): ?>
                  <p><strong>Transferable skills</strong></p>
                  <?php echo ($programme['transferable_skills_learning_outcomes']); ?>
                <?php endif; ?>
                ]]>
              </div>
            </learningOutcome>
          <?php endif; ?>
          <?php if (isset($programme['programme_aims'])): ?>
            <mlo:objective>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <![CDATA[<?php echo ($programme['programme_aims']); ?>]]>
              </div>
            </mlo:objective>
          <?php endif; ?>
          <?php if (isset($programme['prerequisite'])): ?>
            <mlo:prerequisite>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <![CDATA[<?php echo ($programme['prerequisite']); ?>]]>
              </div>
            </mlo:prerequisite>
          <?php endif; ?>
          <?php if ($globalsettings->regulations): ?>
            <regulations>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <![CDATA[<?php echo ($globalsettings->regulations); ?>]]>
              </div>
            </regulations>
          <?php endif; ?>

          <?php foreach ($programme['award'] as $award) : ?>
            <mlo:qualification>
              <dc:identifier><![CDATA[<?php echo ($programme['url']) ?>]]></dc:identifier>
              <dc:title><![CDATA[<?php echo ($award['name']); ?>]]> <?php echo ($programme['programme_title']); ?></dc:title>
              <abbr><![CDATA[<?php echo ($award['name']); ?>]]></abbr>
              <?php if (isset($programme['description'])): ?>
                <dc:description>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <![CDATA[<?php echo ($programme['description']); ?>]]>
                  </div>
                </dc:description>
              <?php endif; ?>
              <?php if (isset($programme['education_level'])): ?>
                <dcterms:educationLevel><![CDATA[<?php echo ($programme['education_level']); ?>]]></dcterms:educationLevel>
              <?php endif; ?>
              <awardedBy><![CDATA[<?php echo ($globalsettings->institution_name); ?>]]></awardedBy>
              <?php if (isset($programme['accredited_by'])): ?>
                <accreditedBy><![CDATA[<?php echo ($programme['accredited_by']); ?>]]></accreditedBy>
              <?php endif; ?>
            </mlo:qualification>
          <?php endforeach; ?>

          <?php if (isset($programme['credits'])) : ?>
          <?php foreach ($programme['credits'] as $credit): ?>
            <mlo:credit>
              <credit:level><![CDATA[<?php echo ($credit->level); ?>]]></credit:level>
              <?php if($credit->scheme): ?>
                <credit:scheme><![CDATA[<?php echo ($credit->scheme); ?>]]></credit:scheme>
              <?php endif; ?>
              <credit:level><![CDATA[<?php echo ($credit->value); ?>]]></credit:level>
            </mlo:credit>
          <?php endforeach; ?>
        <?php endif; ?>
            <presentation>
              <dc:identifier><![CDATA[<?php echo ($programme['url']); ?>]]></dc:identifier>
              <?php if (isset($presentation->subjects)): ?>
                <?php foreach ($presentation->subjects as $subject): ?>
                  <dc:subject><![CDATA[<?php echo ($subject); ?>]]></dc:subject>
                <?php endforeach; ?>
              <?php endif; ?>
              <mlo:start>September <![CDATA[<?php echo ($programme['year']); ?>]]></mlo:start>
              <end>September <?php echo "2016"; ?></end>
              <mlo:duration><![CDATA[<?php echo ($programme['duration']); ?>]]></mlo:duration>
              <applyTo><![CDATA[<?php echo ($programme['url']); ?>]]></applyTo>
              <studyMode identifier="<?php echo "FT"; ?>"><?php echo "Full time"; ?></studyMode>
              <attendanceMode identifier="<?php echo ($programme['attendance_mode_id']); ?>"><?php echo ($programme['attendance_mode']); ?></attendanceMode>
              <?php if ($programme['attendance_pattern']): ?>
                <attendancePattern identifier="<?php echo ($programme['attendance_pattern_id']); ?>"><?php echo ($programme['attendance_pattern']); ?></attendancePattern>
              <?php endif; ?>
              <mlo:languageOfInstruction>en</mlo:languageOfInstruction>
              <languageOfAssessment>en</languageOfAssessment>
              <mlo:cost><![CDATA[<?php echo (strip_tags($programme['cost'])); ?>]]></mlo:cost>
                <venue>
                  <provider>
                    <?php if (isset($programme['location']['description'])): ?>
                      <dc:description>
                        <div xmlns="http://www.w3.org/1999/xhtml">
                          <![CDATA[<?php echo ($programme['location']['description']); ?>]]>
                        </div>
                      </dc:description>
                    <?php endif; ?>
                    <dc:identifier>asc:<?php echo ($programme['location']['name']); ?></dc:identifier>
                    <dc:title><![CDATA[asc:<?php echo ($programme['location']['title']); ?>]]></dc:title>
                    <mlo:location>
                      <?php if(!empty($programme['location']['town'])): ?>
                        <mlo:town><![CDATA[<?php echo ($programme['location']['town']); ?>]]></mlo:town>
                      <?php endif; ?>
                      <?php if(!empty($programme['location']['postcode'])): ?>
                        <mlo:postcode><![CDATA[<?php echo ($programme['location']['postcode']); ?>]]></mlo:postcode>
                      <?php endif; ?>
                      <mlo:address><![CDATA[<?php echo ($programme['location']['address_1']); ?>]]></mlo:address>
                      <?php if(!empty($programme['location']['phone'])): ?>
                        <mlo:phone><![CDATA[<?php echo ($programme['location']['phone']); ?>]]></mlo:phone>
                      <?php endif; ?>
                      <?php if(!empty($programme['location']['fax'])): ?>
                        <mlo:fax><![CDATA[<?php echo ($programme['location']['fax']); ?>]]></mlo:fax>
                      <?php endif; ?>
                      <?php if(!empty($programme['location']['email'])): ?>
                        <mlo:email><![CDATA[<?php echo ($programme['location']['email']); ?>]]></mlo:email>
                      <?php endif; ?>
                      <?php if(!empty($programme['location']['url'])): ?>
                        <mlo:url><![CDATA[<?php echo ($programme['location']['url']); ?>]]></mlo:url>
                      <?php endif; ?>
                    </mlo:location>
                  </provider>
                </venue>
            </presentation>
        </course>
      <?php endforeach; ?>
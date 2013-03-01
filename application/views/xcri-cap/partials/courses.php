      <?php foreach ($programmes as $programme): ?>
        <course>
          <dc:description>
            <div xmlns="http://www.w3.org/1999/xhtml">
              <?php echo strip_tags($programme->programme_overview_text); ?>
            </div>
          </dc:description>
          <dc:identifier><?php echo $programme->url; ?></dc:identifier>
          <?php if (isset($programme->subjects)): ?>
            <?php foreach ($programme->subjects as $subject): ?>
              <dc:subject><?php echo $subject ?></dc:subject>
            <?php endforeach; ?>
          <?php endif; ?>
          <dc:title><?php echo $programme->programme_title; ?></dc:title>
          <dc:type><?php echo "undergraduate"; ?></dc:type>
          <mlo:url><?php echo $programme->url; ?></mlo:url>
          <?php if (isset($programme->programme_abstract)): ?>
            <abstract><?php echo $programme->programme_abstract; ?></abstract>
          <?php endif; ?>
          <?php if(isset($programme->how_to_apply)): ?>
            <applicationProcedure>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->how_to_apply; ?>
              </div>
            </applicationProcedure>
          <?php endif; ?>
          <?php if(isset($programme->teaching_and_assessment)): ?>
            <mlo:assessment>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->teaching_and_assessment; ?>
              </div>
            </mlo:assessment>
          <?php endif; ?>
          <?php if (isset($programme->learning_outcomes)): ?>
            <learningOutcome>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->learning_outcomes; ?>
              </div>
            </learningOutcome>
          <?php endif; ?>
          <?php if (isset($programme->objective)): ?>
            <mlo:objective>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->objective; ?>
              </div>
            </mlo:objective>
          <?php endif; ?>
          <?php if (isset($programme->prerequisite)): ?>
            <mlo:prerequisite>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->prerequisite; ?>
              </div>
            </mlo:prerequisite>
          <?php endif; ?>
          <?php if ($globalsettings->regulations): ?>
            <regulations>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $globalsettings->regulations; ?>
              </div>
            </regulations>
          <?php endif; ?>

          <?php if (isset($programme->award)) : ?>
            <mlo:qualification>
              <dc:identifier><?php echo $programme->url ?></dc:identifier>
              <dc:title><?php echo $programme->award->name; ?> <?php echo $programme->programme_title; ?></dc:title>
              <abbr><?php echo $programme->award->name; ?></abbr>
              <?php if (isset($programme->award->description)): ?>
                <dc:description>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $programme->award->description; ?>
                  </div>
                </dc:description>
              <?php endif; ?>
              <?php if (isset($programme->education_level)): ?>
                <dcterms:educationLevel><?php echo $programme->award->education_level; ?></dcterms:educationLevel>
              <?php endif; ?>
              <awardedBy><?php echo $globalsettings->institution_name; ?></awardedBy>
              <?php if (isset($programme->accredited_by)): ?>
                <accreditedBy><?php echo $programme->award->accredited_by; ?></accreditedBy>
              <?php endif; ?>
            </mlo:qualification>
        <?php endif; ?>

          <?php if (isset($programme->credits)) : ?>
          <?php foreach ($programme->credits as $credit): ?>
            <mlo:credit>
              <credit:level><?php echo $credit->level; ?></credit:level>
              <?php if($credit->scheme): ?>
                <credit:scheme><?php echo $credit->scheme; ?></credit:scheme>
              <?php endif; ?>
              <credit:level><?php echo $credit->value; ?></credit:level>
            </mlo:credit>
          <?php endforeach; ?>
        <?php endif; ?>
            <presentation>
              <dc:identifier><?php echo $programme->url; ?></dc:identifier>
              <?php if (isset($presentation->subjects)): ?>
                <?php foreach ($presentation->subjects as $subject): ?>
                  <dc:subject><?php echo $subject; ?></dc:subject>
                <?php endforeach; ?>
              <?php endif; ?>
              <mlo:start>September <?php echo $programme->year; ?></mlo:start>
              <end>September <?php echo "2016"; ?></end>
              <mlo:duration><?php echo $programme->duration; ?></mlo:duration>
              <applyTo><?php echo $programme->url; ?></applyTo>
              <studyMode identifier="<?php echo "FT"; ?>"><?php echo "Full time"; ?></studyMode>
              <attendanceMode identifier="<?php echo $programme->attendance_mode_id; ?>"><?php echo $programme->attendance_mode; ?></attendanceMode>
              <?php if ($programme->attendance_pattern): ?>
                <attendancePattern identifier="<?php echo $programme->attendance_pattern_id; ?>"><?php echo $programme->attendance_pattern; ?></attendancePattern>
              <?php endif; ?>
              <mlo:languageOfInstruction>en</mlo:languageOfInstruction>
              <languageOfAssessment>en</languageOfAssessment>
              <mlo:cost><?php echo $programme->cost; ?></mlo:cost>
                <venue>
                  <provider>
                    <?php if (isset($programme->campus->description)): ?>
                      <dc:description>
                        <div xmlns="http://www.w3.org/1999/xhtml">
                          <?php echo $programme->campus->description; ?>
                        </div>
                      </dc:description>
                    <?php endif; ?>
                    <dc:identifier>asc:<?php echo $programme->campus->name; ?></dc:identifier>
                    <dc:title>asc:<?php echo $programme->campus->title; ?></dc:title>
                    <mlo:location>
                      <?php if(isset($programme->campus->town)): ?>
                        <mlo:town><?php echo $programme->campus->town; ?></mlo:town>
                      <?php endif; ?>
                      <?php if(isset($programme->campus->postcode)): ?>
                        <mlo:postcode><?php echo $programme->campus->postcode; ?></mlo:postcode>
                      <?php endif; ?>
                      <mlo:address><?php echo $programme->campus->address; ?></mlo:address>
                      <?php if(isset($programme->campus->phone)): ?>
                        <mlo:phone><?php echo $programme->campus->phone; ?></mlo:phone>
                      <?php endif; ?>
                      <?php if(isset($programme->campus->fax)): ?>
                        <mlo:fax><?php echo $programme->campus->fax; ?></mlo:fax>
                      <?php endif; ?>
                      <?php if(isset($programme->campus->email)): ?>
                        <mlo:email><?php echo $programme->campus->email; ?></mlo:email>
                      <?php endif; ?>
                      <?php if(isset($programme->campus->url)): ?>
                        <mlo:url><?php echo $programme->campus->url; ?></mlo:url>
                      <?php endif; ?>
                    </mlo:location>
                  </provider>
                </venue>
            </presentation>
        </course>
      <?php endforeach; ?>
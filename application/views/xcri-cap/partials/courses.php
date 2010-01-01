      <?php foreach ($programmes as $programme): ?>
        <course>

          <?php if ($programme->description): ?>
            <dc:description>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->description; ?>
              </div>
            </dc:description>
          <?php endif; ?>

          <dc:identifier><?php echo $programme->identifier; ?></dc:identifier>

          <?php if ($programme->subjects): ?>
            <?php foreach ($programme->subjects as $subject): ?>
              <dc:subject><?php echo $subject ?></dc:subject>
            <?php endforeach; ?>
          <?php endif; ?>
          
          <dc:title><?php echo $programme->title; ?></dc:title>

          <?php if ($programme->type): ?>
            <dc:type><?php echo $programme->type; ?></dc:type>
          <?php endif; ?>

          <mlo:url><?php echo $programme->url; ?></mlo:url>

          <?php if($programme->abstract): ?>
            <abstract><?php echo $programme->abstract; ?></abstract>
          <?php endif; ?>

          <?php if($programme->application_procedure): ?>
            <applicationProcedure>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->application_procedure; ?>
              </div>
            </applicationProcedure>
          <?php endif; ?>

          <?php if($programme->assessment): ?>
            <mlo:assessment>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->assessment; ?>
              </div>
            </mlo:assessment>
          <?php endif; ?>

          <?php if ($programme->learning_outcome): ?>
            <learningOutcome>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->learning_outcome; ?>
              </div>
            </learningOutcome>
          <?php endif; ?>

          <?php if ($programme->objective): ?>
            <mlo:objective>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->objective; ?>
              </div>
            </mlo:objective>
          <?php endif; ?>

          <?php if ($programme->prerequisite): ?>
            <mlo:prerequisite>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->prerequisite; ?>
              </div>
            </mlo:prerequisite>
          <?php endif; ?>

          <?php if ($programme->regulations): ?>
            <regulations>
              <div xmlns="http://www.w3.org/1999/xhtml">
                <?php echo $programme->regulations; ?>
              </div>
            </regulations>
          <?php endif; ?>

          <?php foreach ($programme->qualifications as $qualification): ?>
            <mlo:qualification>
              
              <dc:identifier><?php echo $qualification->identifier; ?></dc:identifier>

              <dc:title><?php echo $qualification->title; ?></dc:title>
              
              <?php if ($qualification->abbr): ?>
                <abbr><?php echo $qualification->abbr; ?></abbr>
              <?php endif; ?>

              <?php if ($qualification->description): ?>
                <dc:description>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $qualification->description; ?>
                  </div>
                </dc:description>
              <?php endif; ?>

              <?php if ($qualification->education_level): ?>
                <dcterms:educationLevel><?php echo $qualification->education_level; ?></dcterms:educationLevel>
              <?php endif; ?>

              <?php if ($qualification->awarded_by): ?>
                <awardedBy><?php echo $qualification->awarded_by; ?></awardedBy>
              <?php endif; ?>

              <?php if ($qualification->accredited_by): ?>
                <accreditedBy><?php echo $qualification->accredited_by; ?></accreditedBy>
              <?php endif; ?>

            </mlo:qualification>
          <?php endforeach; ?>

          <?php foreach ($programme->credits as $credit): ?>
            <mlo:credit>
              <credit:level><?php echo $credit->level; ?></credit:level>
              <?php if($credit->scheme): ?>
                <credit:scheme><?php echo $credit->scheme; ?></credit:scheme>
              <?php endif; ?>
              <credit:level><?php echo $credit->value; ?></credit:level>
            </mlo:credit>
          <?php endforeach; ?>

          <?php foreach ($programme->presentations as $presentation): ?>
            <presentation>

              <?php if ($presentation->description): ?>
                <dc:description>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $presentation->description; ?>
                  </div>
                </dc:description>
              <?php endif; ?>

              <dc:identifier><?php echo $presentation->identifier; ?></dc:identifier>

              <?php if ($presentation->subjects): ?>
                <?php foreach ($presentation->subjects as $subject): ?>
                  <dc:subject><?php echo $subject ?></dc:subject>
                <?php endforeach; // subjects ?>
              <?php endif; ?>

              <dc:title><?php echo $presentation->title; ?></dc:title>

              <?php if($presentation->abstract): ?>
                <abstract><?php echo $presentation->abstract; ?></abstract>
              <?php endif; ?>

              <?php if($presentation->application_procedure): ?>
                <applicationProcedure>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $presentation->application_procedure; ?>
                  </div>
                </applicationProcedure>
              <?php endif; ?>

              <?php if($presentation->assessment): ?>
                <mlo:assessment>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $presentation->assessment; ?>
                  </div>
                </mlo:assessment>
              <?php endif; ?>

              <?php if ($presentation->learning_outcome): ?>
                <learningOutcome>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $presentation->learning_outcome; ?>
                  </div>
                </learningOutcome>
              <?php endif; ?>

              <?php if ($presentation->objective): ?>
                <mlo:objective>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $presentation->objective; ?>
                  </div>
                </mlo:objective>
              <?php endif; ?>

              <?php if ($presentation->prerequisite): ?>
                <mlo:prerequisite>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $presentation->prerequisite; ?>
                  </div>
                </mlo:prerequisite>
              <?php endif; ?>

              <?php if ($presentation->regulations): ?>
                <regulations>
                  <div xmlns="http://www.w3.org/1999/xhtml">
                    <?php echo $presentation->regulations; ?>
                  </div>
                </regulations>
              <?php endif; ?>

              <?php if($presentation->start): ?>
                <mlo:start><?php echo $presentation->start; ?></mlo:start>
              <?php endif; ?>

              <?php if($presentation->end): ?>
                <end><?php echo $presentation->end; ?></end>
              <?php endif; ?>

              <?php if($presentation->duration): ?>
                <mlo:duration><?php echo $presentation->duration; ?></mlo:duration>
              <?php endif; ?>

              <?php if($presentation->apply_from): ?>
                <applyFrom><?php echo $presentation->apply_from; ?></applyFrom>
              <?php endif; ?>

              <?php if($presentation->apply_until): ?>
                <applyUntil><?php echo $presentation->apply_until; ?></applyUntil>
              <?php endif; ?>

              <?php if($presentation->apply_to): ?>
                <applyTo><?php echo $presentation->apply_to; ?></applyTo>
              <?php endif; ?>
                
              <?php if ($presentation->study_mode_id && $presentation->study_mode): ?>
                <studyMode identifier="<?php echo $presentation->study_mode_id; ?>"><?php echo $presentation->study_mode; ?></studyMode>
              <?php endif; ?>

              <?php if ($presentation->attendance_mode_id && $presentation->attendance_mode): ?>
                <attendanceMode identifier="<?php echo $presentation->attendance_mode_id; ?>"><?php echo $presentation->attendance_mode; ?></attendanceMode>
              <?php endif; ?>

              <?php if ($presentation->attendance_pattern_id && $presentation->attendance_pattern): ?>
                <attendancePattern identifier="<?php echo $presentation->attendance_pattern_id; ?>"><?php echo $presentation->attendance_pattern; ?></attendancePattern>
              <?php endif; ?>

              <?php if ($presentation->language_of_instruction_id && $presentation->language_of_instruction): ?>
                <mlo:languageOfInstruction><?php echo $presentation->language_of_instruction_id; ?></mlo:languageOfInstruction>
              <?php endif; ?>

              <?php if ($presentation->language_of_assessment_id && $presentation->language_of_assessment): ?>
                <languageOfAssessment><?php echo $presentation->language_of_assessment_id; ?></languageOfAssessment>
              <?php endif; ?>

              <?php if($presentation->places): ?>
                <mlo:places><?php echo $presentation->places; ?></mlo:places>
              <?php endif; ?>

              <?php if ($presentation->cost): ?>
                <mlo:cost><?php echo $presentation->cost; ?></mlo:cost>
              <?php endif; ?>

              <?php if ($presentation->age): ?>
                <age><?php echo $presentation->age; ?></age>
              <?php endif; ?>

              <?php foreach ($presentation->venues as $venue): ?>
                <venue>
                  <provider>
                    
                    <?php if ($venue->description): ?>
                      <dc:description>
                        <div xmlns="http://www.w3.org/1999/xhtml">
                          <?php echo $venue->description; ?>
                        </div>
                      </dc:description>
                    <?php endif; ?>

                    <dc:identifier><?php echo $venue->identifier; ?></dc:identifier>

                    <dc:title><?php echo $venue->title; ?></dc:title>

                    <mlo:location>
                      <?php if($venue->location->town): ?>
                        <mlo:town><?php echo $venue->location->town; ?></mlo:town>
                      <?php endif; ?>

                      <?php if($venue->location->postcode): ?>
                        <mlo:postcode><?php echo $venue->location->postcode; ?></mlo:postcode>
                      <?php endif; ?>

                      <mlo:address><?php echo $venue->location->address; ?></mlo:address>

                      <?php if($venue->location->phone): ?>
                        <mlo:phone><?php echo $venue->location->phone; ?></mlo:phone>
                      <?php endif; ?>

                      <?php if($venue->location->fax): ?>
                        <mlo:fax><?php echo $venue->location->fax; ?></mlo:fax>
                      <?php endif; ?>

                      <?php if($venue->location->email): ?>
                        <mlo:email><?php echo $venue->location->email; ?></mlo:email>
                      <?php endif; ?>

                      <?php if($venue->location->url): ?>
                        <mlo:url><?php echo $venue->location->url; ?></mlo:url>
                      <?php endif; ?>

                    </mlo:location>

                  </provider>
                </venue>
              <?php endforeach; ?>

            </presentation>
          <?php endforeach; ?>

        </course>
      <?php endforeach; ?>
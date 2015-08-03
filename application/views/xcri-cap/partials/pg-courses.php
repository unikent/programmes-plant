      <?php foreach ($programmes as $programme): ?>
        <?php foreach ($programme['award'] as $award) : ?>
          <course>
            <mlo:isPartOf><?php echo $programme['administrative_school']['name']; ?></mlo:isPartOf>
            <dc:description>
              <xhtml:div>
                <?php echo XMLHelper::makeXMLSafe($programme['programme_overview']); ?>
              </xhtml:div>
            </dc:description>
            <dc:identifier><?php echo ($programme['url']); ?></dc:identifier>
            <?php if (isset($programme['subjects'])): ?>
              <?php foreach ($programme['subjects'] as $subject): ?>
                <?php if (!empty($subject)): ?>
                  <dc:subject><?php echo XMLHelper::makeXMLSafe($subject['name']) ?></dc:subject>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php endif; ?>
            <dc:title><?php echo XMLHelper::makeXMLSafe($programme['programme_title']); ?></dc:title>
            <dc:type><?php echo __("programmes.{$programme['type']}"); ?></dc:type>
            <dc:type xsi:type="courseDataProgramme:courseTypeGeneral" courseDataProgramme:identifier="PG"><?php echo ucfirst(__("programmes.{$programme['type']}")); ?></dc:type>
            <dc:type xsi:type="mlo:RTCourseTypeFlag" mlo:RT-identifier="<?php echo $programme['programme_type'] === 'taught' ? 'T' : 'R'; ?>"><?php echo $programme['programme_type'] === 'taught' ? 'Taught' : 'Research'; ?></dc:type>
            <mlo:url><?php echo ($programme['url']); ?></mlo:url>
            <?php if (isset($programme['programme_abstract'])): ?>
              <abstract><?php echo XMLHelper::makeXMLSafe(strip_tags($programme['programme_abstract'])); ?></abstract>
            <?php endif; ?>
            <?php if(!empty($programme['how_to_apply'])): ?>
              <applicationProcedure>
                <xhtml:div>
                  <?php echo XMLHelper::makeXMLSafe($programme['how_to_apply']); ?>
                </xhtml:div>
              </applicationProcedure>
            <?php endif; ?>
            <?php if(!empty($programme['teaching_and_assessment'])): ?>
              <mlo:assessment>
                <xhtml:div>
                  <?php echo XMLHelper::makeXMLSafe($programme['teaching_and_assessment']); ?>
                </xhtml:div>
              </mlo:assessment>
            <?php endif; ?>
            <?php if (isset($programme['learning_outcomes'])): ?>
              <learningOutcome>
                <xhtml:div>
                  <p><strong>Knowledge and understanding</strong></p>
                  <?php echo XMLHelper::makeXMLSafe($programme['learning_outcomes']); ?>

                  <?php if (isset($programme['intellectual_skills_learning_outcomes'])): ?>
                    <p><strong>Intellectual Skills</strong></p>
                    <?php echo XMLHelper::makeXMLSafe($programme['intellectual_skills_learning_outcomes']); ?>
                  <?php endif; ?>

                  <?php if (isset($programme['subjectspecific_skills_learning_outcomes'])): ?>
                    <p><strong>Subject-specific skills</strong></p>
                    <?php echo XMLHelper::makeXMLSafe($programme['subjectspecific_skills_learning_outcomes']); ?>
                  <?php endif; ?>

                  <?php if (isset($programme['transferable_skills_learning_outcomes'])): ?>
                    <p><strong>Transferable skills</strong></p>
                    <?php echo XMLHelper::makeXMLSafe($programme['transferable_skills_learning_outcomes']); ?>
                  <?php endif; ?>
                </xhtml:div>
              </learningOutcome>
            <?php endif; ?>
            <?php if (isset($programme['programme_aims'])): ?>
              <mlo:objective>
                <xhtml:div>
                  <?php echo XMLHelper::makeXMLSafe($programme['programme_aims']); ?>
                </xhtml:div>
              </mlo:objective>
            <?php endif; ?>
            <?php if (isset($programme['entry_requirements']) || isset($programme['pg_general_entry_requirements']) || isset($programme['english_language_requirements_intro_text'])): ?>
              <mlo:prerequisite>
                <xhtml:div>
                  <?php if (isset($programme['entry_requirements'])): ?>
                    <xhtml:h3>Entry requirements</xhtml:h3>
                    <?php echo XMLHelper::makeXMLSafe($programme['entry_requirements']); ?>
                  <?php endif; ?>
                  <?php if (isset($programme['pg_general_entry_requirements'])): ?>
                    <xhtml:h3>General entry requirements</xhtml:h3>
                    <?php echo XMLHelper::makeXMLSafe($programme['pg_general_entry_requirements']); ?>
                  <?php endif; ?>
                  <?php if (isset($programme['english_language_requirements_intro_text'])): ?>
                    <xhtml:h3>English language requirements</xhtml:h3>
                    <?php echo XMLHelper::makeXMLSafe($programme['english_language_requirements_intro_text']); ?>
                  <?php endif; ?>
                </xhtml:div>
              </mlo:prerequisite>
            <?php endif; ?>
            <?php if ($globalsettings->regulations): ?>
              <regulations>
                <xhtml:div>
                  <?php echo XMLHelper::makeXMLSafe($globalsettings->regulations); ?>
                </xhtml:div>
              </regulations>
            <?php endif; ?>

            <mlo:qualification>
              <dc:identifier><?php echo ($award['name']) ?></dc:identifier>
              <dc:title><?php echo XMLHelper::makeXMLSafe($programme['programme_title']); ?></dc:title>
              <abbr><?php echo ($award['name']); ?></abbr>
              <?php if (isset($programme['description'])): ?>
                <dc:description>
                  <xhtml:div>
                    <?php echo XMLHelper::makeXMLSafe($programme['description']); ?>
                  </xhtml:div>
                </dc:description>
              <?php endif; ?>
              <?php if (isset($programme['education_level'])): ?>
                <dcterms:educationLevel><?php echo XMLHelper::makeXMLSafe($programme['education_level']); ?></dcterms:educationLevel>
              <?php endif; ?>
              <awardedBy><?php echo ($globalsettings->institution_name); ?></awardedBy>
              <?php if (isset($programme['accredited_by'])): ?>
                <accreditedBy><?php echo ($programme['accredited_by']); ?></accreditedBy>
              <?php endif; ?>
            </mlo:qualification>

            <?php if (isset($programme['credits'])) : ?>
              <?php foreach ($programme['credits'] as $credit): ?>
                <mlo:credit>
                  <credit:level><?php echo ($credit->level); ?></credit:level>
                  <?php if($credit->scheme): ?>
                    <credit:scheme><?php echo ($credit->scheme); ?></credit:scheme>
                  <?php endif; ?>
                  <credit:level><?php echo ($credit->value); ?></credit:level>
                </mlo:credit>
              <?php endforeach; ?>
            <?php endif; ?>
              <presentation>
                <dc:identifier><?php echo ($programme['url']); ?></dc:identifier>
                <?php foreach ($programme['subjects'] as $subject): ?>
                  <?php if (!empty($subject)): ?>
                    <dc:subject><?php echo XMLHelper::makeXMLSafe($subject['name']) ?></dc:subject>
                  <?php endif; ?>
                <?php endforeach; ?>

                <mlo:start dtf="<?php echo $programme['start_date']; ?>"><?php echo $programme['start_date_text']; ?></mlo:start>
                <mlo:duration interval="<?php echo $programme['duration_text_id']; ?>"><?php echo ($programme['duration_text']); ?></mlo:duration>

                <applyTo><?php echo ($programme['url']); ?></applyTo>
                <?php foreach ($programme['modes_of_study'] as $mode): ?>
                    <studyMode identifier="<?php echo $mode['id']; ?>"><?php echo $mode['name']; ?></studyMode>
                <?php endforeach; ?>
                <attendanceMode identifier="<?php echo $programme['attendance_mode_id']; ?>"><?php echo $programme['attendance_mode']; ?></attendanceMode>
                <?php if ($programme['attendance_pattern']): ?>
                  <attendancePattern identifier="<?php echo $programme['attendance_pattern_id'] ?>"><?php echo $programme['attendance_pattern'] ?></attendancePattern>
                <?php endif; ?>
                <mlo:languageOfInstruction>en</mlo:languageOfInstruction>
                <languageOfAssessment>en</languageOfAssessment>
                <mlo:cost>
                    <?php echo XMLHelper::makeXMLSafe($programme['cost']); ?>
                    <?php $pos_codes = array(); ?>
                    <?php foreach ($programme['deliveries'] as $delivery): ?>
                      <?php if ( $delivery['award_name'] === $award['name'] && !in_array($delivery['pos_code'], $pos_codes) ): ?>

                        <xhtml:table>
                          <xhtml:thead>
                            <xhtml:tr>
                              <xhtml:th><xhtml:strong><?php echo $delivery['award_name'] ?></xhtml:strong></xhtml:th>
                              <xhtml:th>UK/EU</xhtml:th>
                              <xhtml:th>Overseas</xhtml:th>
                            </xhtml:tr>
                            <xhtml:tr>
                              <xhtml:td colspan="3"><?php echo $delivery['description'] ?></xhtml:td>
                            </xhtml:tr>
                          </xhtml:thead>
                          <xhtml:tbody>



                                            <?php if ($has_parttime): ?>

                                            <?php endif; ?>
                            <?php if ($programme['has_fulltime']): ?>
                              <xhtml:tr>
                                <xhtml:td><xhtml:strong>Full-time</xhtml:strong></xhtml:td>
                                <xhtml:td><?php echo empty($delivery['fees']['home']['full-time']) ? ((empty($delivery['fees']['home']['euro-full-time'])) ? 'TBC' : 'EUR ' . number_format($delivery['fees']['home']['euro-full-time'])) : 'GBP ' . number_format($delivery['fees']['home']['full-time']); ?></xhtml:td>
                                <xhtml:td><?php echo empty($delivery['fees']['int']['full-time']) ? ((empty($delivery['fees']['int']['euro-full-time'])) ? 'TBC' : 'EUR ' . number_format($delivery['fees']['int']['euro-full-time'])) : 'GBP ' . number_format($delivery['fees']['int']['full-time']); ?></xhtml:td>
                              </xhtml:tr>
                            <?php endif; ?>
                            <?php if ($programme['has_parttime']): ?>
                              <xhtml:tr>
                                <xhtml:td><xhtml:strong>Part-time</xhtml:strong></xhtml:td>
                                <xhtml:td><?php echo empty($delivery['fees']['home']['part-time']) ? ((empty($delivery['fees']['home']['euro-part-time'])) ? 'TBC' : 'EUR ' . number_format($delivery['fees']['home']['euro-part-time'])) : 'GBP ' . number_format($delivery['fees']['home']['part-time']); ?></xhtml:td>
                                <xhtml:td><?php echo empty($delivery['fees']['int']['part-time']) ? ((empty($delivery['fees']['int']['euro-part-time'])) ? 'TBC' : 'EUR ' . number_format($delivery['fees']['int']['euro-part-time'])) : 'GBP ' . number_format($delivery['fees']['int']['part-time']); ?></xhtml:td>
                              </xhtml:tr>
                            <?php endif; ?>
                          </xhtml:tbody>
                        </xhtml:table>

                        <?php $pos_codes[] = $delivery['pos_code'];?>
                      <?php endif; ?>
                    <?php endforeach; ?>

                </mlo:cost>
                <venue>
                  <provider>
                    <?php if (isset($programme['location']['description'])): ?>
                      <dc:description>
                        <xhtml:div>
                          <?php echo ($programme['location']['description']); ?>
                        </xhtml:div>
                      </dc:description>
                    <?php endif; ?>
                    <dc:identifier>asc:<?php echo XMLHelper::htmlTrim($programme['location']['name']); ?></dc:identifier>
                    <dc:title>asc:<?php echo XMLHelper::htmlTrim($programme['location']['title']); ?></dc:title>
                    <mlo:location>
                      <?php if(!empty($programme['location']['town'])): ?>
                        <mlo:town><?php echo XMLHelper::htmlTrim($programme['location']['town']); ?></mlo:town>
                      <?php endif; ?>
                      <?php if(!empty($programme['location']['postcode'])): ?>
                        <mlo:postcode><?php echo XMLHelper::htmlTrim($programme['location']['postcode']); ?></mlo:postcode>
                      <?php endif; ?>
                      <mlo:address><?php echo XMLHelper::htmlTrim($programme['location']['address_2']); ?></mlo:address>
                      <mlo:address><?php echo XMLHelper::htmlTrim($programme['location']['town']); ?></mlo:address>
                      <?php if(!empty($programme['enquiry_phone'])): ?>
                        <mlo:phone><?php echo XMLHelper::htmlTrim($programme['enquiry_phone']); ?></mlo:phone>
                      <?php endif; ?>
                      <?php if(!empty($programme['enquiry_fax'])): ?>
                        <mlo:fax><?php echo XMLHelper::htmlTrim($programme['enquiry_fax']); ?></mlo:fax>
                      <?php endif; ?>
                      <?php if(!empty($programme['enquiry_email'])): ?>
                        <mlo:email><?php echo XMLHelper::htmlTrim($programme['enquiry_email']); ?></mlo:email>
                      <?php endif; ?>
                      <?php if(!empty($programme['location']['url'])): ?>
                        <mlo:url><?php echo XMLHelper::htmlTrim($programme['location']['url']); ?></mlo:url>
                      <?php endif; ?>
                    </mlo:location>
                  </provider>
                </venue>
              </presentation>
          </course>
        <?php endforeach; ?>
      <?php endforeach; ?>

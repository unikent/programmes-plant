<?php foreach ($programmes as $programme): ?>
	<course>
		<dc:description>
			<xhtml:div>
				<?php echo XMLHelper::makeXMLSafe(strip_tags($programme['programme_overview_text'])); ?>
			</xhtml:div>
		</dc:description>
		<dc:identifier xsi:type="courseDataProgramme:internalID"><?php echo ($programme['url']); ?></dc:identifier>
		<?php if (isset($programme['subjects'])): ?>
			<?php foreach ($programme['subjects'] as $subject): ?>
				<?php if (!empty($subject)): ?>
					<dc:subject><?php echo XMLHelper::makeXMLSafe($subject['name']) ?></dc:subject>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<dc:title><?php echo XMLHelper::makeXMLSafe($programme['programme_title']); ?></dc:title>
		<dc:type><?php echo __("programmes.{$programme['type']}"); ?></dc:type>
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

		<?php if (isset($programme['prerequisite'])): ?>
			<mlo:prerequisite>
				<xhtml:div>
					<?php echo XMLHelper::makeXMLSafe($programme['prerequisite']); ?>
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

		<?php foreach ($programme['award'] as $award) : ?>
			<mlo:qualification>
				<dc:identifier><?php echo XMLHelper::makeXMLSafe($award['name']); ?></dc:identifier>
				<dc:title><?php echo XMLHelper::makeXMLSafe($award['name']) . ' ' . XMLHelper::makeXMLSafe($programme['programme_title']); ?></dc:title>
				<abbr><?php echo XMLHelper::makeXMLSafe($award['name']); ?></abbr>
				<?php if (isset($programme['description'])): ?>
					<dc:description>
						<xhtml:div>
							<?php echo XMLHelper::makeXMLSafe($programme['description']); ?>
						</xhtml:div>
					</dc:description>
				<?php endif; ?>
				<dcterms:educationLevel><?php echo ucfirst(__("programmes.{$programme['type']}")); ?></dcterms:educationLevel>
				<awardedBy><?php echo ($globalsettings->institution_name); ?></awardedBy>
				<?php if (isset($programme['accredited_by'])): ?>
					<accreditedBy><?php echo XMLHelper::makeXMLSafe($programme['accredited_by']); ?></accreditedBy>
				<?php endif; ?>
			</mlo:qualification>
		<?php endforeach; ?>

		<?php if (isset($programme['credits'])) : ?>
			<?php foreach ($programme['credits'] as $credit): ?>
				<mlo:credit>
					<credit:level><?php echo XMLHelper::makeXMLSafe($credit->level); ?></credit:level>
					<?php if($credit->scheme): ?>
						<credit:scheme><?php echo XMLHelper::makeXMLSafe($credit->scheme); ?></credit:scheme>
					<?php endif; ?>
					<credit:level><?php echo XMLHelper::makeXMLSafe($credit->value); ?></credit:level>
				</mlo:credit>
			<?php endforeach; ?>
		<?php endif; ?>

		<presentation>
			<dc:identifier><?php echo XMLHelper::makeXMLSafe($programme['url']); ?></dc:identifier>
			<?php if (isset($presentation->subjects)): ?>
				<?php foreach ($presentation->subjects as $subject): ?>
					<dc:subject><?php echo XMLHelper::makeXMLSafe($subject); ?></dc:subject>
				<?php endforeach; ?>
			<?php endif; ?>
			<mlo:start>September <?php echo XMLHelper::makeXMLSafe($programme['year']); ?></mlo:start>
			<end>September <?php echo "2016"; ?></end>
			<mlo:duration><?php echo XMLHelper::makeXMLSafe($programme['duration']); ?></mlo:duration>
			<applyTo><?php echo XMLHelper::makeXMLSafe($programme['url']); ?></applyTo>
			<studyMode identifier="<?php echo $programme['mode_of_study_id']; ?>"><?php echo $programme['mode_of_study']; ?></studyMode>
			<attendanceMode identifier="<?php echo $programme['attendance_mode_id']; ?>"><?php echo $programme['attendance_mode']; ?></attendanceMode>
			<?php if ($programme['attendance_pattern']): ?>
				<attendancePattern><?php echo ($programme['attendance_pattern']); ?></attendancePattern>
			<?php endif; ?>
			<mlo:languageOfInstruction>en</mlo:languageOfInstruction>
			<languageOfAssessment>en</languageOfAssessment>
			<mlo:cost>
			<?php
			$cost = '';
			$fulltime_used = false;

			foreach ($programme['deliveries'] as $delivery) {
				if ( $delivery['award_name'] === $award['name'] && !in_array($delivery['pos_code'], $pos_codes) ){
					if ($programme['has_fulltime'] && $delivery['attendance_pattern'] === 'full-time') {
						$fulltime_used = true;
						$cost = 'Full Time UK/EU: ';
						$cost .= empty($delivery['fees']['home']['full-time']) ? ((empty($delivery['fees']['home']['euro-full-time'])) ? 'TBC' :
						number_format($delivery['fees']['home']['euro-full-time'])) . ' EUR' :
						number_format($delivery['fees']['home']['full-time']) . ' GBP';
						$cost .= ' | Full Time Overseas: ';
						$cost .= empty($delivery['fees']['int']['full-time']) ? ((empty($delivery['fees']['int']['euro-full-time'])) ? 'TBC' :
						number_format($delivery['fees']['int']['euro-full-time'])) . ' EUR' :
						number_format($delivery['fees']['int']['full-time']) . ' GBP';
					}

					if ($fulltime_used && $delivery['attendance_pattern'] === 'part-time') {
						$cost .= ($programme['has_fulltime']) ? ' | ' :
						'';
						$cost .= 'Part Time UK/EU: ';
						$cost .= empty($delivery['fees']['home']['part-time']) ? ((empty($delivery['fees']['home']['euro-part-time'])) ? 'TBC' :
						number_format($delivery['fees']['home']['euro-part-time'])) . ' EUR' :
						number_format($delivery['fees']['home']['part-time']) . ' GBP';
						$cost .= ' | Part Time Overseas: N/A';
					}
				}
			}

			$trimmed = trim($cost);

			if (empty($trimmed)) {
				$cost = 'TBC';
			}
			echo $cost;
			?>
			</mlo:cost>
			<venue>
				<provider>
					<?php if (isset($programme['location']['description'])): ?>
						<dc:description>
							<xhtml:div>
								<?php echo XMLHelper::makeXMLSafe($programme['location']['description']); ?>
							</xhtml:div>
						</dc:description>
					<?php endif; ?>
					<dc:identifier><?php echo XMLHelper::htmlTrim($programme['location']['name']); ?></dc:identifier>
					<dc:title><?php echo XMLHelper::htmlTrim($programme['location']['title']); ?></dc:title>
					<mlo:location>
						<?php if(!empty($programme['location']['town'])): ?>
							<mlo:town><?php echo XMLHelper::htmlTrim($programme['location']['town']); ?></mlo:town>
						<?php endif; ?>
						<?php if(!empty($programme['location']['postcode'])): ?>
							<mlo:postcode><?php echo XMLHelper::htmlTrim($programme['location']['postcode']); ?></mlo:postcode>
						<?php endif; ?>
						<mlo:address><?php echo XMLHelper::htmlTrim($programme['location']['address_2']); ?></mlo:address>
						<mlo:address><?php echo XMLHelper::htmlTrim($programme['location']['town']); ?></mlo:address>
						<?php if(!empty($programme['location']['phone'])): ?>
							<mlo:phone><?php echo XMLHelper::htmlTrim($programme['location']['phone']); ?></mlo:phone>
						<?php endif; ?>
						<?php if(!empty($programme['location']['fax'])): ?>
							<mlo:fax><?php echo XMLHelper::htmlTrim($programme['location']['fax']); ?></mlo:fax>
						<?php endif; ?>
						<?php if(!empty($programme['location']['email'])): ?>
							<mlo:email><?php echo XMLHelper::htmlTrim($programme['location']['email']); ?></mlo:email>
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

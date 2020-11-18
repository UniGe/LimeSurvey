<?php

/** @var QuestionAdministrationController $this */
/** @var Question $question */
/** @var array $advancedSettings */

?>

<div class="col-12 scope-apply-base-style scope-min-height">
    <div class="container-fluid" id="advanced-options-container">
        <div class="row scoped-tablist-container">
            <!-- Advanced settings tabs -->
            <ul class="nav nav-tabs scoped-tablist-advanced-settings" role="tablist">
                <?php if ($question->questionType->subquestions > 0): ?>
                    <li role="presentation">
                        <a
                            href="#subquestions"
                            aria-controls="subquestions"
                            role="tab"
                            data-toggle="tab"
                        >
                            <?= gT('Subquestions'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($question->questionType->answerscales > 0): ?>
                <li role="presentation">
                    <a
                        href="#answeroptions"
                        aria-controls="answeroptions"
                        role="tab"
                        data-toggle="tab"
                    >
                        <?= gT('Answer options'); ?>
                    </a>
                </li>
                <?php endif;

                if ($question->questionType->hasdefaultvalues > 0)
                {
                ?>

                    <li role="presentation">
                        <a
                            href="#defaultanswers"
                            aria-controls="defaultanswers"
                            role="tab"
                            data-toggle="tab"
                        >
                            <?= gT('Default Answers'); ?>
                        </a>
                    </li>
                <?php
                }
                foreach ($advancedSettings as $category => $_) : ?>
                    <?php if ($category === 'Display'): ?>
                        <li role="presentation" class="active">
                    <?php else: ?>
                        <li role="presentation">
                    <?php endif; ?>
                        <a
                            href="#<?= $category; ?>"
                            aria-controls="<?= $category; ?>"
                            role="tab"
                            data-toggle="tab"
                            >
                            <!-- TODO: Localization -->
                            <?= $category; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php if ($question->questionType->subquestions > 0): ?>
                    <div role="tabpanel" class="tab-pane" id="subquestions">
                        <!-- TODO: Add path in controller. -->
                        <?php Yii::app()->twigRenderer->getLoader()->addPath(__DIR__, '__main__'); ?>
                        <?= Yii::app()->twigRenderer->renderViewFromFile(
                            '/application/views/questionAdministration/subquestions.twig',
                            [
                                'activated'    => $question->survey->active !== 'N',
                                'scalecount'   => 1,
                                'subquestions' => $question->subquestions ? $question->subquestions : [$question->getEmptySubquestion()],
                                'question'     => $question,
                                'allLanguages' => $question->survey->allLanguages,
                                'language'     => $question->survey->language,
                                'hasLabelSetPermission' => Permission::model()->hasGlobalPermission('labelsets','create'),
                            ],
                            true
                        ); ?>
                    </div>
                <?php endif; ?>
                <?php if ($question->questionType->answerscales > 0): ?>
                    <div role="tabpanel" class="tab-pane" id="answeroptions">
                        <!-- TODO: Add path in controller. -->
                        <?php Yii::app()->twigRenderer->getLoader()->addPath(__DIR__, '__main__'); ?>
                        <?= Yii::app()->twigRenderer->renderViewFromFile(
                            '/application/views/questionAdministration/answerOptions.twig',
                            [
                                'activated'  => $question->survey->active !== 'N',
                                'oldCode'    => true,
                                'scalecount' => 1,
                                'answers'    => $question->answers ? $question->answers : [$question->getEmptyAnswerOption()],
                                'question'     => $question,
                                'allLanguages' => $question->survey->allLanguages,
                                'language'   => $question->survey->language,
                                'hasLabelSetPermission' => Permission::model()->hasGlobalPermission('labelsets','create'),
                            ],
                            true
                        ); ?>
                    </div>
                <?php endif;
                    //try to get hasdefaultvalues from new implementation (xml files, table question_themes)
                    $questionMetaDataSettings = (QuestionTheme::findQuestionMetaData($question->type))['settings'] ;
                ?>
                <?php if($questionMetaDataSettings->hasdefaultvalues == 1){ ?>
                    <div role="tabpanel" class="tab-pane" id="defaultanswers">
                        <!-- TODO: Add path in controller. -->
                        <?php
                        //should only be rendered when question already exists ... (like in master before)
                        if ($question->qid !== null && $question->qid !==0 ) {
                            Yii::app()->getController()->renderPartial('editdefaultvalues', [
                                'oSurvey'             => $question->survey,
                                //'questionMetaData'        => QuestionType::modelsAttributes(),
                                'questionMetaDataSettings'        => $questionMetaDataSettings,
                                'questionrow'         => $question->attributes,
                                'question'            => $question,
                                'hasUpdatePermission' => Permission::model()->hasSurveyPermission(
                                    $question->sid,
                                    'surveycontent',
                                    'update'
                                ) ? '' : 'disabled="disabled" readonly="readonly"'
                            ]);
                        }else{ //default values can only be added/saved if subquestions already exist ... ?>
                            <div id='edit-question-body' class='side-body <?php echo getSideBodyClass(false); ?>'>
                                <h3>
                                    <?php eT('Edit default answer values') ?>
                                </h3>
                                <div class="row">
                                      First create question/subquestions and save them. Afterwards you can edit default answers.
                                </div>
                            </div>
                        <?php }
                        ?>
                    </div>
                <?php }
                foreach ($advancedSettings as $category => $settings): ?>
                    <?php if ($category === 'Display'): ?>
                        <div role="tabpanel" class="tab-pane active" id="<?= $category; ?>">
                    <?php else: ?>
                        <div role="tabpanel" class="tab-pane" id="<?= $category; ?>">
                    <?php endif; ?>
                        <?php foreach ($settings as $setting): ?>
                            <?php $this->widget('ext.AdvancedSettingWidget.AdvancedSettingWidget', ['setting' => $setting]); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

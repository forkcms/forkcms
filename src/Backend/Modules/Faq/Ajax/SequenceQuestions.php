<?php

namespace Backend\Modules\Faq\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * Reorder questions
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class SequenceQuestions extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $questionId = \SpoonFilter::getPostValue('questionId', null, '', 'int');
        $fromCategoryId = \SpoonFilter::getPostValue('fromCategoryId', null, '', 'int');
        $toCategoryId = \SpoonFilter::getPostValue('toCategoryId', null, '', 'int');
        $fromCategorySequence = \SpoonFilter::getPostValue('fromCategorySequence', null, '', 'string');
        $toCategorySequence = \SpoonFilter::getPostValue('toCategorySequence', null, '', 'string');

        // invalid question id
        $question = BackendFaqModel::get($questionId);
        if (!empty($questionId)) {
            // list ids
            $fromCategorySequence = (array) explode(',', ltrim($fromCategorySequence, ','));
            $toCategorySequence = (array) explode(',', ltrim($toCategorySequence, ','));

            // is the question moved to a new category?
            if ($fromCategoryId != $toCategoryId) {
                $question->setCategory(BackendFaqModel::getCategory($toCategoryId));

                BackendFaqModel::update($question);
                $this->resequenceQuestionIds($toCategorySequence);
            }

            $this->resequenceQuestionIds($fromCategorySequence);

            // success output
            $this->output(self::OK, null, 'sequence updated');
        } else {
            $this->output(self::BAD_REQUEST, null, 'question does not exist');
        }
    }

    /**
     * Set an ascending sequence on a list of question Id's
     *
     * @param  array  $questionIds The id's of the questions to sequence
     */
    protected function resequenceQuestionIds(array $questionIds)
    {
        foreach ($questionIds as $i => $id) {
            $questionToSequence = BackendFaqModel::get((int) $id);

            if (!empty($questionToSequence)) {
                $questionToSequence->setSequence($i + 1);
                BackendFaqModel::update($questionToSequence);
            }
        }
    }
}

<?php
//******************************************************************************
//                    ConcernedItemGridViewWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 23 Aug, 2018
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use yii\base\Widget;
use Yii;
use app\models\yiiModels\YiiConcernedItemModel;
use yii\grid\GridView;
use app\components\helpers\Vocabulary;

/**
 * A widget used to generate a customisable concerned item GridView interface
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
abstract class ConcernedItemGridViewWidget extends Widget {

    CONST CONCERNED_ITEMS = "concernedItems";
    CONST NO_CONCERNED_ITEMS = "No items concerned";
    
    /**
     * Define the concerned items list to show
     * @var mixed
     */
    public $concernedItems;

    public function init() {
        parent::init();
        // must be not null
        if ($this->concernedItems === null) {
            throw new \Exception("Concerned items aren't set");
        }
    }

    /**
     * Render the concerned item list
     * @return string the HTML string rendered
     */
    public function run() {
        if ($this->concernedItems->getCount() == 0) {
            $htmlRendered = "<h3>" . Yii::t('app', 'No item concerned') . "</h3>";
        } else {
            $htmlRendered = "<h3>" . Yii::t('app', 'Concerned Items') . "</h3>";
            $htmlRendered .= GridView::widget([
                        'dataProvider' => $this->concernedItems,
                        'columns' => $this->getColumns(),
            ]);
        }
        return $htmlRendered;
    }
    
    /**
     * Returns the columns of the GridView.
     * @return array
     */
    protected abstract function getColumns(): array;
}

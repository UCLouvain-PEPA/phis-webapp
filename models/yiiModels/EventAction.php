<?php
//******************************************************************************
//                               EventAction.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 06 March 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use Yii;
use app\models\yiiModels\YiiEventModel;
use app\models\wsModels\WSConstants;

/**
 * Model regrouping the common attributes of both creation and update event models.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventAction extends YiiEventModel {
    
    const EVENT_UNUPDATABLE_DUE_TO_UNUPDATABLE_PROPRTY_LABEL = 
            'The event cannot be updated because one of its specific property'
            . ' isn\'t manageable by the interface';
    
    /**
     * Date timezone offset.
     * @example +01:00
     * @var string
     */
    public $dateTimezoneOffset;
    const DATE_TIMEZONE_OFFSET = 'dateTimezoneOffset';
    const DATE_TIMEZONE_OFFSET_LABEL = 'Timezone offset';
    
    /**
     * Date without timezone.
     * @example 1899-12-31T12:00:00
     * @var string
     */
    public $dateWithoutTimezone;
    const DATE_WITHOUT_TIMEZONE = 'dateWithoutTimezone';
    const DATE_WITHOUT_TIMEZONE_LABEL = self::DATE_LABEL;
    
    /**
     * Concerned items URIs.
     * @example http://www.opensilex.org/demo/DMO2011-1
     * @var array of strings
     */
    public $concernedItemsUris;
    const CONCERNED_ITEMS_URIS = 'concernedItemsUris';
    const CONCERNED_ITEMS_URIS_LABEL = 'Concerned items URIs';
    
    /**
     * Specific property hasPest.
     * @var YiiPropertyModel
     */
    public $propertyHasPest;
    const PROPERTY_HAS_PEST = 'propertyHasPest';
    const PROPERTY_HAS_PEST_LABEL = 'hasPest';

    /**
     * Specific property set.
     * @var YiiPropertyModel
     */
    public $propertySet;
    const PROPERTY_SET = 'propertySet';
    const PROPERTY_SET_LABEL = 'Parameter (for PhotoPeriod in hour [ex:16], for Fertigation in second [ex: D 5/115 N 5/1155], )';
    
    /**
     * Specific properties from.
     * @var YiiPropertyModel
     */
    public $propertyFrom;
    const PROPERTY_FROM = 'propertyFrom';
    const PROPERTY_FROM_LABEL = 'From';

    /**
     * Specific properties ph.
     * @var YiiPropertyModel
     */
    public $propertypH;
    const PROPERTY_PH = 'propertypH';
    const PROPERTY_PH_LABEL = 'pH [ex: 6.41])';

    /**
     * Specific properties conductivity.
     * @var YiiPropertyModel
     */
    public $propertyConductivity;
    const PROPERTY_CONDUCTIVITY = 'propertyConductivity';
    const PROPERTY_CONDUCTIVITY_LABEL = 'Conductivity (in microsiemens/centimeter [ex: 1150])';

    /**
     * Specific properties pressure.
     * @var YiiPropertyModel
     */
    public $propertyPressure;
    const PROPERTY_PRESSURE = 'propertyPressure';
    const PROPERTY_PRESSURE_LABEL = 'Pressure (in bar [ex: 5.3])';

    /**
     * Specific properties HasAmount.
     * @var YiiPropertyModel
     */
    public $propertyHasAmount;
    const PROPERTY_HASAMOUNT = 'propertyHasAmount';
    const PROPERTY_HASAMOUNT_LABEL = 'Put a global % for GerminationGlobal [ex: 95] or a integer of non-germinated plant for GerminationPlant [ex: 8] ';

    /**
     * Specific properties HasAmount.
     * @var YiiPropertyModel
     */
    public $propertyHasDocument;
    const PROPERTY_HASDOCUMENT = 'propertyHasDocument';
    const PROPERTY_HASDOCUMENT_LABEL = 'Select the corresponding document, if the document is not there add it for the AeroponicUnit';    
    
    /**
     * Specific properties HasUnit.
     * @var YiiPropertyModel
     */
    public $propertyHasUnit;
    const PROPERTY_HASUNIT = 'propertyHasUnit';
    const PROPERTY_HASUNIT_LABEL = 'HasUnit';

    /**
     * Specific properties With.
     * @var YiiPropertyModel
     */
    public $propertyWith;
    const PROPERTY_WITH = 'propertyWith';
    const PROPERTY_WITH_LABEL = 'With';

    /**
     * Specific properties to.
     * @var YiiPropertyModel
     */
    public $propertyTo;
    const PROPERTY_TO = 'propertyTo';
    const PROPERTY_TO_LABEL = 'To';
    
    /**
     * Specific properties associated with.
     * @var YiiPropertyModel
     */
    public $propertyAssociatedToASensor;
    const PROPERTY_ASSOCIATED_TO_A_SENSOR = 'propertyAssociatedToASensor';
    const PROPERTY_ASOOCIATED_TO_A_SENSOR_LABEL = 'Associated to a sensor';
    
    /**
     * Specific properties associated by.
     * @var YiiPropertyModel
     */
    public $propertyBy;
    const PROPERTY_BY = 'propertyBy';
    const PROPERTY_BY_LABEL = 'By';
    

    /**
     * Specific properties type.
     * @var YiiPropertyModel
     */
    public $propertyType;
    const PROPERTY_TYPE = 'propertyType';
    const PROPERTY_TYPE_LABEL = 'Property type';

    /**
     * The return URL after annotation creation.
     * @var string 
     */
    public $returnUrl;
    const RETURN_URL = "returnUrl";
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [[
                self::TYPE, 
                self::DATE_WITHOUT_TIMEZONE,
                self::DATE_TIMEZONE_OFFSET,
                self::DATE_WITHOUT_TIMEZONE,
                self::CONCERNED_ITEMS_URIS
            ],  'required'],
            [[
                self::PROPERTY_HAS_PEST, 
                self::PROPERTY_FROM,
                self::PROPERTY_PH,
                self::PROPERTY_CONDUCTIVITY,
                self::PROPERTY_PRESSURE,
		        self::PROPERTY_WITH,
                self::PROPERTY_SET,
                self::PROPERTY_HASAMOUNT,
                self::PROPERTY_HASDOCUMENT,
                self::PROPERTY_HASUNIT,  
                self::PROPERTY_TO, 
                self::PROPERTY_ASSOCIATED_TO_A_SENSOR,
                self::PROPERTY_BY, 
                self::RETURN_URL,
            ],  'safe']
        ]; 
    }
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return array_merge(
            parent::attributeLabels(),
            [
                self::CONCERNED_ITEMS_URIS => Yii::t('app', self::CONCERNED_ITEMS_URIS_LABEL),
                self::DATE_TIMEZONE_OFFSET => Yii::t('app', self::DATE_TIMEZONE_OFFSET_LABEL),
                self::DATE_WITHOUT_TIMEZONE => Yii::t('app', self::DATE_WITHOUT_TIMEZONE_LABEL),
                self::PROPERTY_HAS_PEST => Yii::t('app', self::PROPERTY_HAS_PEST_LABEL),
                self::PROPERTY_FROM => Yii::t('app', self::PROPERTY_FROM_LABEL),
                self::PROPERTY_PH => Yii::t('app', self::PROPERTY_PH_LABEL),
                self::PROPERTY_SET => Yii::t('app', self::PROPERTY_SET_LABEL),
                self::PROPERTY_CONDUCTIVITY => Yii::t('app', self::PROPERTY_CONDUCTIVITY_LABEL),
                self::PROPERTY_PRESSURE => Yii::t('app', self::PROPERTY_PRESSURE_LABEL),
		        self::PROPERTY_WITH => Yii::t('app', self::PROPERTY_WITH_LABEL),
                self::PROPERTY_HASAMOUNT => Yii::t('app', self::PROPERTY_HASAMOUNT_LABEL),
                self::PROPERTY_HASDOCUMENT => Yii::t('app', self::PROPERTY_HASDOCUMENT_LABEL),
                self::PROPERTY_HASUNIT=> Yii::t('app', self::PROPERTY_HASUNIT_LABEL),
                self::PROPERTY_TO => Yii::t('app', self::PROPERTY_TO_LABEL),
                self::PROPERTY_BY => Yii::t('app', self::PROPERTY_BY_LABEL),
                self::PROPERTY_ASSOCIATED_TO_A_SENSOR => Yii::t('app', self::PROPERTY_ASOOCIATED_TO_A_SENSOR_LABEL),
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function attributesToArray() {
        $propertiesArray = [];
        foreach ($this->properties as $property) {
            if(isset($property)) {
                $propertiesArray[] = $property->attributesToArray();
            }
        }
        return [
            self::TYPE => $this->rdfType,
            self::DATE => $this->dateWithoutTimezone.$this->dateTimezoneOffset,
            self::CONCERNED_ITEMS_URIS => $this->concernedItemsUris,
            self::PROPERTIES => $propertiesArray,
        ];
    }

    /**
     * Gets the event corresponding to the given URI.
     * @param type $sessionToken
     * @param type $uri
     * @return $this
     */
    public function getEvent($sessionToken, $uri) {
        $event = parent::getEvent($sessionToken, $uri);
        if (!is_string($event)) {
            if (isset($event[WSConstants::TOKEN_INVALID])) {
                return $event;
            } else {
                $this->dateWithoutTimezone = str_replace('T', ' ', substr($event->date, 0, -6));
                $this->dateTimezoneOffset = substr($event->date, -6);
                return $this;
            }
        } else {
            return $event;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);
        $this->dateWithoutTimezone = str_replace(" ", "T", $this->dateWithoutTimezone);
        $this->properties = [$this->getPropertyInCreation()];
    }
    
    /**
     * Gets a property object according to the data entered in the creation form.
     * @param type $eventModel
     */
    private function getPropertyInCreation() {
        $property = new YiiPropertyModel();
        switch ($this->rdfType) {
            case Yii::$app->params['MoveFrom']:
                $property->value = $this->propertyFrom;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['From'];
                break;
            case Yii::$app->params['FertigationRefillWith']:
                $property->value = $this->propertyWith;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['With'];
                break;
            case Yii::$app->params['MoveTo']:
                $property->value = $this->propertyTo;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['To'];
                break;
            case Yii::$app->params['AssociatedToASensor']:
                $property->value = $this->propertyAssociatedToASensor;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['associatedToASensor'];
                break;
            case Yii::$app->params['PartReplacementBy']:
                $property->value = $this->propertyBy;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['By'];
                break;
            case Yii::$app->params['FertigationMeasurementpH']:
                $property->value = $this->propertypH;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['pH'];
                break;
            case Yii::$app->params['FertigationMeasurementConductivity']:
                $property->value = $this->propertyConductivity;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['Conductivity'];
                break;
            case Yii::$app->params['FertigationMeasurementPressure']:
                $property->value = $this->propertyPressure;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['Pressure'];
                break;
            case Yii::$app->params['FertigationAutoRefillOnWith']:
                $property->value = $this->propertyWith;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['With'];
                break;
            case Yii::$app->params['FertigationReplaceWith']:
                $property->value = $this->propertyWith;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['With'];
                break;
            case Yii::$app->params['FertigationSolutionSet']:
                $property->value = $this->propertyWith;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['With'];
                break;
            case Yii::$app->params['FertigationCycleSet']:
                $property->value = $this->propertySet;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['Set'];
                break;
            case Yii::$app->params['ArtificialLightPhotoperiodSet']:
                $property->value = $this->propertySet;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['Set'];
                break;
            case Yii::$app->params['ArtificialLightSpectrumSet']:
                $property->value = $this->propertyWith;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['With'];
                break;
            case Yii::$app->params['GerminationPlant']:
                $property->value = $this->propertyHasAmount;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['HasAmount'];
                break;
            case Yii::$app->params['GerminationGlobal']:
                $property->value = $this->propertyHasAmount;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['HasAmount'];
                break;
            case Yii::$app->params['Treatment']:
                $property->value = $this->propertyHasDocument;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['HasDocument'];
                break;
            case Yii::$app->params['SetQRList']:
                $property->value = $this->propertyHasDocument;
                $property->rdfType = $this->propertyType;
                $property->relation = Yii::$app->params['HasDocument'];
                break;                                
            default : 
                $property = null;
                break;
        }
        return $property;
    }
}

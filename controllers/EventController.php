<?php
//******************************************************************************
//                          EventController.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: Jan. 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\controllers;

use Yii;
use app\controllers\GenericController;
use app\models\yiiModels\EventSearch;
use app\models\yiiModels\DocumentSearch;
use app\models\yiiModels\YiiUserModel;
use app\models\yiiModels\YiiEventModel;
use app\models\yiiModels\EventCreation;
use app\models\yiiModels\EventUpdate;
use app\models\yiiModels\ProjectSearch;
use app\models\yiiModels\ActuatorSearch;
use app\models\yiiModels\VectorSearch;
use app\models\yiiModels\VariableSearch;
use app\models\yiiModels\ScientificObjectSearch;
use app\models\yiiModels\UnitSearch;
use app\models\yiiModels\ExperimentSearch;
use app\models\yiiModels\InfrastructureSearch;
use app\models\wsModels\WSConstants;
use app\components\helpers\SiteMessages;

/**
 * Controller for the events.
 * @see yii\web\Controller
 * @see app\models\yiiModels\YiiEventModel
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventController extends GenericController {    
    
    const PARAM_ANNOTATIONS_DATA_PROVIDER = "paramAnnotations";
    const PARAM_UPDATABLE = "paramUpdatable";
    
    const ANNOTATIONS_PAGE = "annotations-page";
    const EVENT_TYPES = "eventTypes";

    const INFRASTRUCTURES_DATA = "infrastructures";
    const INFRASTRUCTURES_DATA_URI = "infrastructureUri";
    const INFRASTRUCTURES_DATA_LABEL = "infrastructureLabel";
    const INFRASTRUCTURES_DATA_TYPE = "infrastructureType";

    const SENSOR_DATA = "sensors";
    const SENSOR_DATA_URI = "sensorUri";
    const SENSOR_DATA_LABEL = "sensorLabel";
    const SENSOR_DATA_TYPE = "sensorType";
    
    const SCIENTIFICOBJECT_DATA = "ScientificObjects";
    const SCIENTIFICOBJECT_DATA_URI = "ScientificObjectsUri";
    const SCIENTIFICOBJECT_DATA_LABEL = "ScientificObjectsLabel";
    const SCIENTIFICOBJECT_DATA_TYPE = "ScientificObjectsType";

    const UNIT_DATA = "Units";
    const UNIT_DATA_URI = "UnitsUri";
    const UNIT_DATA_LABEL = "UnitsLabel";
    const UNIT_DATA_TYPE = "UnitsType";

    const ACTUATOR_DATA = "actuators";
    const ACTUATOR_DATA_URI = "actuatorUri";
    const ACTUATOR_DATA_LABEL = "actuatorLabel";
    const ACTUATOR_DATA_TYPE = "actuatorType";

    const VECTOR_DATA = "vectors";
    const VECTOR_DATA_URI = "vectorUri";
    const VECTOR_DATA_LABEL = "vectorLabel";
    const VECTOR_DATA_TYPE = "vectorType";

    const VARIABLE_DATA = "variables";
    const VARIABLE_DATA_URI = "variableUri";
    const VARIABLE_DATA_LABEL = "variableLabel";
    const VARIABLE_DATA_TYPE = "variableType";

    const USER_DATA = "users";
    const USER_DATA_URI = "userUri";
    const USER_DATA_EMAIL = "userEmail";
    const USER_DATA_TYPE = "userType";

    const EXPERIMENT_DATA = "experiments";
    const EXPERIMENT_DATA_URI = "experimentUri";
    const EXPERIMENT_DATA_ALIAS = "experimentAlias";
    const EXPERIMENT_DATA_TYPE = "experimentType";

    const DOCUMENT_DATA = "documents";
    const DOCUMENT_DATA_URI = "documentUri";
    const DOCUMENT_DATA_TITLE = "documentTitle";
    const DOCUMENT_DATA_TYPE = "documentType";

    const PARAM_CONCERNED_ITEMS_URIS = 'concernedItemsUris';
    const TYPE = 'type';
    const PARAM_RETURN_URL = "returnUrl";

    /**
     * The return URL after annotation creation.
     * @var string 
     */
    public $returnUrl;
    
    /**
     * Lists the events.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EventSearch();
        
        $searchParams = Yii::$app->request->queryParams;
        
        if (isset($searchParams[WSConstants::PAGE])) {
            $searchParams[WSConstants::PAGE] = $searchParams[WSConstants::PAGE] - 1;
        }
        $searchParams[WSConstants::PAGE_SIZE] = Yii::$app->params['indexPageSize'];
        
        $searchResult = $searchModel->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);
        
        if (is_string($searchResult)) {
            if ($searchResult === WSConstants::TOKEN_INVALID) {
                return $this->redirect(Yii::$app->urlManager->createUrl(SiteMessages::SITE_LOGIN_PAGE_ROUTE));
            } else {
                return $this->render(SiteMessages::SITE_ERROR_PAGE_ROUTE, [
                    SiteMessages::SITE_PAGE_NAME => SiteMessages::INTERNAL_ERROR,
                    SiteMessages::SITE_PAGE_MESSAGE => $searchResult]);
            }
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel, 
                'dataProvider' => $searchResult]);
        }
    }

    /**
     * Displays the detail of an event.
     * @param $id URI of the event
     * @return mixed redirect in case of error otherwise return the "view" view
     */
    public function actionView($id) {
        // Get request parameters
        $searchParams = Yii::$app->request->queryParams;
        
        // Get event
        $event = (new YiiEventModel())->getEvent(Yii::$app->session[WSConstants::ACCESS_TOKEN], $id);

        // Get documents
        $searchDocumentModel = new DocumentSearch();
        $searchDocumentModel->concernedItemFilter = $id;
        $documentProvider = $searchDocumentModel->search(
                Yii::$app->session[WSConstants::ACCESS_TOKEN], 
                [YiiEventModel::CONCERNED_ITEMS => $id]);
        
        // Get annotations
        $annotationProvider = $event->getEventAnnotations(Yii::$app->session[WSConstants::ACCESS_TOKEN], $searchParams);
        $annotationProvider->pagination->pageParam = self::ANNOTATIONS_PAGE;

        // Render the view of the event
        if (is_array($event) && isset($event[WSConstants::TOKEN_INVALID])) {
            return redirectToLoginPage();
        } else {
            return $this->render('view', [
                'model' =>  $event,
                'dataDocumentsProvider' => $documentProvider,
                self::PARAM_ANNOTATIONS_DATA_PROVIDER => $annotationProvider,
                self::PARAM_UPDATABLE => !$this->hasUnupdatableProperties($event)   
            ]);
        }
    }
    
    private function hasUnupdatableProperties($eventAction) : bool {
        foreach($eventAction->properties as $property) {
            if($property->relation !== Yii::$app->params['from']
                && $property->relation !== Yii::$app->params['to']) {
            return true;
            }
        }
        return false;
    }
    
    /**
     * Gets the event types URIs.
     * @return event types URIs 
     */
    public function getSensorTypes() {
        $model = new \app\models\yiiModels\YiiSensorModel();
        
        $sensorsTypes = [];
        $model->page = 0;
        $model->pageSize = Yii::$app->params['webServicePageSizeMax'];
        $sensorsTypesConcepts = $model->getSensorsTypes(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
        if ($sensorsTypesConcepts === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($sensorsTypesConcepts[WSConstants::DATA] as $sensorType) {
                $sensorsTypes[$sensorType->uri] = $sensorType->uri;
            }
        }
        
        return $sensorsTypes;
    }
    
    /**
     * Gets the event types URIs.
     * @return event types URIs 
     */
    public function getEventsTypes() {
        $model = new YiiEventModel();
        
        $eventsTypes = [];
        $model->page = 0;
        $model->pageSize = Yii::$app->params['webServicePageSizeMax'];
        $eventsTypesConcepts = $model->getEventsTypes(Yii::$app->session[WSConstants::ACCESS_TOKEN]);
        if ($eventsTypesConcepts === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($eventsTypesConcepts[WSConstants::DATA] as $eventType) {
                $eventsTypes[$eventType->uri] = $eventType->uri;
            }
        }
        
        return $eventsTypes;
    }
    
    /**
     * Gets all infrastructures.
     * @return experiments 
     */
    public function getInfrastructuresUrisTypesLabels() {
        $model = new InfrastructureSearch();
        $model->page = 10000;
        $infrastructuresUrisTypesLabels = [];
        $infrastructures = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($infrastructures === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($infrastructures->models as $infrastructure) {
                $infrastructuresUrisTypesLabels[] =
                    [
                        self::INFRASTRUCTURES_DATA_URI => $infrastructure->uri,
                        self::INFRASTRUCTURES_DATA_LABEL => $infrastructure->label,
                        self::INFRASTRUCTURES_DATA_TYPE => $infrastructure->rdfType
                    ];
            }
        }
        
        return $infrastructuresUrisTypesLabels;
    }

    /**
     * Gets all sensors.
     * @return sensors 
     */
    public function getSensorsUrisTypesLabels() {
        $model = new \app\models\yiiModels\SensorSearch();
        $model->page = 0;
        $model->pageSize = 10000;
        $sensorsUrisTypesLabels = [];
        $sensors = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($sensors === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($sensors->models as $sensor) {
                $sensorsUrisTypesLabels[] =
                    [
                        self::SENSOR_DATA_URI => $sensor->uri,
                        self::SENSOR_DATA_LABEL => $sensor->label,
                        self::SENSOR_DATA_TYPE => $sensor->rdfType
                    ];
            }
        }
        
        return $sensorsUrisTypesLabels;
    }

    /**
     * Gets all scientific objects.
     * @return scientificobjects 
     */
    public function getScientificObjectsUrisTypesLabels() {
        $model = new ScientificObjectSearch();
        $model->page = 0;
        $model->pageSize = 1000000;
        $ScientificObjectsUrisTypesLabels = [];
        $ScientificObjects= $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($ScientificObjects === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($ScientificObjects->models as $ScientificObject) {
                $ScientificObjectsUrisTypesLabels[] =
                    [
                        self::SCIENTIFICOBJECT_DATA_URI => $ScientificObject->uri,
                        self::SCIENTIFICOBJECT_DATA_LABEL => $ScientificObject->label,
                        self::SCIENTIFICOBJECT_DATA_TYPE => $ScientificObject->rdfType
                    ];
            }
        }
        return $ScientificObjectsUrisTypesLabels;
    }

    /**
     * Gets all Units.
     * @return units 
     */
    public function getUnitsUrisTypesLabels() {
        $model = new UnitSearch();
        $model->page = 0;
        $model->pageSize = 1000000;
        $UnitsUrisTypesLabels = [];
        $Units= $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($Units === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($Units->models as $Unit) {
                $UnitsUrisTypesLabels[] =
                    [
                        self::UNIT_DATA_URI => $Unit->uri,
                        self::UNIT_DATA_LABEL => $Unit->label,
                        self::UNIT_DATA_TYPE => $Unit->rdfType
                    ];
            }
        }
        return $UnitsUrisTypesLabels;
    }
    

    /**
     * Gets all actuators.
     * @return actuators 
     */
    public function getActuatorsUrisTypesLabels() {
        $model = new \app\models\yiiModels\ActuatorSearch();
        $model->page = 0;
        $model->pageSize = 10000;
        $actuatorsUrisTypesLabels = [];
        $actuators = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($actuators === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($actuators->models as $actuator) {
                $actuatorsUrisTypesLabels[] =
                    [
                        self::ACTUATOR_DATA_URI => $actuator->uri,
                        self::ACTUATOR_DATA_LABEL => $actuator->label,
                        self::ACTUATOR_DATA_TYPE => $actuator->rdfType
                    ];
            }
        }
        return $actuatorsUrisTypesLabels;
    }

    /**
     * Gets all vectors.
     * @return vectors 
     */
    public function getVectorsUrisTypesLabels() {
        $model = new \app\models\yiiModels\VectorSearch();
        $model->page = 0;
        $model->pageSize = 10000;
        $vectorsUrisTypesLabels = [];
        $vectors = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($vectors === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($vectors->models as $vector) {
                $vectorsUrisTypesLabels[] =
                    [
                        self::VECTOR_DATA_URI => $vector->uri,
                        self::VECTOR_DATA_LABEL => $vector->label,
                        self::VECTOR_DATA_TYPE => $vector->rdfType
                    ];
            }
        }
        return $vectorsUrisTypesLabels;
    }

    /**
     * Gets all variables.
     * @return variables 
     */
    public function getVariablesUrisTypesLabels() {
        $model = new \app\models\yiiModels\VariableSearch();
        $model->page = 0;
        $model->pageSize = 10000;
        $variablesUrisTypesLabels = [];
        $variables = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($variables === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($variables->models as $variable) {
                $variablesUrisTypesLabels[] =
                    [
                        self::VARIABLE_DATA_URI => $variable->uri,
                        self::VARIABLE_DATA_LABEL => $variable->label,
                        self::VARIABLE_DATA_TYPE => $variabel->rdfType
                    ];
            }
        }
        return $variablesUrisTypesLabels;
    }

    /**
     * Gets all users.
     * @return users 
     */
    public function getUsersUrisTypesLabels() {
        $model = new \app\models\yiiModels\UserSearch();
        $model->page = 0;
        $model->pageSize = 10000;
        $usersUrisTypesLabels = [];
        $users = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($users === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($users->models as $user) {
                $usersUrisTypesLabels[] =
                    [
                        self::USER_DATA_URI => $user->uri,
                        self::USER_DATA_EMAIL => $user->email,
                        self::USER_DATA_TYPE => $user->rdfType
                    ];
            }
        }
        return $usersUrisTypesLabels;
    }

    /**
     * Gets all experiments.
     * @return experiments 
     */
    public function getExperimentsUrisTypesLabels() {
        $model = new \app\models\yiiModels\ExperimentSearch();
        $model->page = 0;
        $model->pageSize = 10000;
        $experimentsUrisTypesLabels = [];
        $experiments = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($experiments === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($experiments->models as $experiment) {
                $experimentsUrisTypesLabels[] =
                    [
                        self::EXPERIMENT_DATA_URI => $experiment->uri,
                        self::EXPERIMENT_DATA_ALIAS => $experiment->alias,
                        self::EXPERIMENT_DATA_TYPE => $experiment->rdfType
                    ];
            }
        }
        return $experimentsUrisTypesLabels;
    }

    /**
     * Gets all documents.
     * @return documents 
     */
    public function getDocumentsUrisTypesLabels() {
        $model = new \app\models\yiiModels\DocumentSearch();
        $model->page = 0;
        $model->pageSize = 10000;
        $documentsUrisTypesLabels = [];
        $documents = $model->search(Yii::$app->session[WSConstants::ACCESS_TOKEN], null);
        if ($documents === WSConstants::TOKEN_INVALID) {
            return WSConstants::TOKEN_INVALID;
        } else {
            foreach ($documents->models as $document) {
                $documentsUrisTypesLabels[] =
                    [
                        self::DOCUMENT_DATA_URI => $document->uri,
                        self::DOCUMENT_DATA_TITLE => $document->title,
                        self::DOCUMENT_DATA_TYPE => $document->rdfType
                    ];
            }
        }
        return $documentsUrisTypesLabels;
    }

    /**
     * Displays the form to create an event or creates it in case of form submission.
     * @return mixed redirect in case of error or after successfully create 
     * the event otherwise return the "create" view.
     */
    public function actionCreate() {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $event = new EventCreation();
        $event->isNewRecord = true;
        
        // Display form
        if (!$event->load(Yii::$app->request->post())) {
            $event->load(Yii::$app->request->get(), '');
            if(Yii::$app->request->get()['type'] === "scientific-objects"){
                 $event->load(array(self::PARAM_CONCERNED_ITEMS_URIS =>array_keys(Yii::$app->session['scientific-object'])),'');
            }
            $event->creator = $this->getCreatorUri($sessionToken);
            $this->loadFormParams();
            return $this->render('create', ['model' =>  $event]);
           
        // Submit form    
        } else {   
            $dataToSend[] = $event->attributesToArray(); 
            $requestResults =  $event->insert($sessionToken, $dataToSend);
            return $this->handlePostPutResponse($requestResults, $event->returnUrl);
        }
    }

    /**
     * Displays the form to update an event.
     * @return mixed redirect in case of error or after successfully updating 
     * the event otherwise returns the "update" view 
     */
    public function actionUpdate($id) {
        $sessionToken = Yii::$app->session[WSConstants::ACCESS_TOKEN];
        $event = new EventUpdate();
        $event->isNewRecord = false;
        
        // Display form
        if (!$event->load(Yii::$app->request->post())) {
            $event = $event->getEvent($sessionToken, $id);
            $this->loadFormParams();
            return $this->render('update', ['model' =>  $event]);
            
        // Submit form  
        } else {
            $dataToSend[] = $event->attributesToArray(); 
            $requestResults =  $event->update($sessionToken, $dataToSend);
            return $this->handlePostPutResponse($requestResults, ['view', 'id' =>  $event->uri]);
        }
    }
    
    /**
     * Loads params used by the forms (creation or update).
     */
    private function loadFormParams() {
        $this->view->params[self::EVENT_TYPES] = $this->getEventsTypes();
        $this->view->params[self::SENSOR_DATA] = $this->getSensorsUrisTypesLabels();
        $this->view->params[self::INFRASTRUCTURES_DATA] = $this->getInfrastructuresUrisTypesLabels();
	    $this->view->params[self::SCIENTIFICOBJECT_DATA] = $this->getScientificObjectsUrisTypesLabels();
        $this->view->params[self::UNIT_DATA] = $this->getUnitsUrisTypesLabels();
        $this->view->params[self::ACTUATOR_DATA] = $this->getActuatorsUrisTypesLabels();
        $this->view->params[self::VECTOR_DATA] = $this->getVectorsUrisTypesLabels();
        $this->view->params[self::VARIABLE_DATA] = $this->getVariablesUrisTypesLabels();
        $this->view->params[self::USER_DATA] = $this->getUsersUrisTypesLabels();
        $this->view->params[self::EXPERIMENT_DATA] = $this->getExperimentsUrisTypesLabels();
        $this->view->params[self::DOCUMENT_DATA] = $this->getDocumentsUrisTypesLabels();
    }
    
    /**
     * Gets the creator of an event.
     */
    private function getCreatorUri($sessionToken) {
        $userModel = new YiiUserModel();
        $userModel->findByEmail($sessionToken, Yii::$app->session['email']);
        return $userModel->uri;
    }
}
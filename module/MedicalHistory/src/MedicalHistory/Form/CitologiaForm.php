<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace MedicalHistory\Form;

use Zend\Form\Form;
use Zend\Db\ResultSet\ResultSet;
use Admissions\Model\Entity\Document;
use Admissions\Model\Entity\DocumentTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class CitologiaForm extends Form
{
    public function __construct($controller = null)
    {
        parent::__construct('first_history');

        $dbAdapter = $controller->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $tableGateway = new TableGateway('hcneuro_tipodoc', $dbAdapter);

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Document());

        $documentTable = new DocumentTable($tableGateway, $dbAdapter, null, $resultSetPrototype);

        $result = $documentTable->fetchAll()->toArray();

        $documents = array();
        foreach ($result as $document) {
            $documents[$document["cod_tip_doc"]] = $document["nom_tip_doc"];
        }

        $this->add(array(
            'name' => 'cod_tip_doc',
            'type' => 'select',
            'options' => array(
                'label' => 'Tipo de documento',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'num_doc_pac',
            'type' => 'text',
            'options' => array(
                'label' => 'Documento',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => 'documento',
                'required' => 'required',
                'class' => 'form-control input-sm',
                'minlength' => 4,
                'maxlength' => 20,
            ),
        ));

        $this->add(array(
            'name' => 'cal_mues_a',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'A. Satisfactoria (Células endocervicales/zona de transformación presente)',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'cal_mues_b',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'B. Satisfactoria (Células endocervicales/zona de transformación ausente)',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'cal_mues_c',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'C. Insatisfecha',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'cal_mues_d',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'C. Rechazada',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'cat_gen_a',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'A. Negativa para lesión intraepitelial',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));


        $this->add(array(
            'name' => 'cat_gen_b',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'B. Anormalidades celulares epiteliales',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'mic_a',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'A. Flora Vaginal Normal',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'mic_b',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'B. Trichomonas Vaginalis',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'mic_c',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'C. Hongos consistentes con Cándida sp',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'mic_d',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'D. Vaginosis Bacteriana',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'mic_e',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'E. Consistente con Actinomyces sp',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'mic_f',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'F. Efectos citopáticos por virus del Herpes Simple',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'mic_g',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'G. Otros',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));


        $this->add(array(
            'name' => 'otr_haz_a',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'A. Cambios celulares reactivos asociados a Inflamación',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'otr_haz_b',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'B. Cambios celulares reactivos asociados a Radiación',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'otr_haz_c',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'C. Cambios celulares a DIU',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));


        $this->add(array(
            'name' => 'otr_haz_d',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'D. Células glandulares post-histerectomía',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'otr_haz_e',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'E. Atrofia',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));


        $this->add(array(
            'name' => 'otr_haz_f',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'F. Células endometriales (después de los 40 años)',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        /* 4. Anormalidades celulas escamosas*/

        $this->add(array(
            'name' => 'ano_cel_esc_a',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'A. Atipias en células escamosas de significado indeterminado (ASC-US)',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

         $this->add(array(
            'name' => 'ano_cel_esc_b',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'B. Atipias en células escamosas de significado que no descartan LEI de alto Grado (ASC-H)',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'ano_cel_esc_c',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'C. Lesión Intraepitelial Escamosa de Bajo Grado, LEI bg (cambios asociados a infección por HPV o displasia ligera NIC I)',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));


        $this->add(array(
            'name' => 'ano_cel_esc_d',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'D. Lesión Intraepitelial Escamosa de Alto Grado, LEI ag (NIC II, NIC III, Ca In Situ)',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'ano_cel_esc_e',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'E. Lesión Intraepitelial Escamosa de Alto Grado sospechoso de infiltración',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'ano_cel_esc_f',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'F. Carcinoma Escamocelular Invasivo',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        /* 4. Anormalidades en celulas glandulares*/

        $this->add(array(
            'name' => 'ano_cel_gla_a',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'A. Células endocervicales atípicas sin ningún otro significado',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'ano_cel_gla_b',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'B. Células endometriales atípicas sin ningún otro significado',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'ano_cel_gla_c',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'C. Células endocervicales atípicas sospechosas de malignidad',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'ano_cel_gla_d',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'D. Células endometriales atípicas sospechosas de malignidad',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

           $this->add(array(
            'name' => 'ano_cel_gla_e',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'E. Adenocarcinoma Endocervical in situ',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        $this->add(array(
            'name' => 'ano_cel_gla_f',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'F. Adenocarcinoma Endocervical',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

             $this->add(array(
            'name' => 'ano_cel_gla_g',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'G. Adenocarcinoma Endometrial',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));


        $this->add(array(
            'name' => 'ano_cel_gla_h',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'H. Otras neoplasias',
                'value_options' => $documents,
            ),
            'attributes' => array(
                'placeholder' => '',
                'required' => ''


            ),
        ));

        /* Observaciones */
         $this->add(array(
            'name' => 'observaciones',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Observaciones',
            ),
            'attributes' => array(
                'class' => 'form-control input-sm',
                'maxlength' => '5000',
                'rows' => '5',
                'cols' => '200'
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Guardar'
            ),
        ));

    }
}

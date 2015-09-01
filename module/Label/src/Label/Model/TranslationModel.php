<?php
namespace Label\Model;

class TranslationModel
{
    private $dbAdapter;

    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }
    
    /**
     * returns existing translations for label whose label id is sent
     * @param integer $labelId
     * @return array
     */
    public function getExistingTranslations($labelId)
    {
        $parameters['label_id'] = $labelId;
        
        $sql = "SELECT l.name, t.id, t.text FROM ht_label_trans t 
            LEFT JOIN language l ON l.lang = t.language
            WHERE t.label_id = :label_id";
        
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($parameters);
        $count = $results->count();
        
        return ['existingTrans' => $results, 'count' => $count];
    }
    
    /**
     * returns not existing translations for label whose label id is sent
     * @param integer $labelId
     * @return array
     */
    public function getNotExistingTranslations($labelId)
    {
        $parameters['label_id'] = $labelId;
        
        $sql = "SELECT lang, name FROM language 
            WHERE lang NOT IN (SELECT language FROM ht_label_trans WHERE label_id = :label_id) 
            AND is_default = 'no'";
        
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($parameters);
        $count = $results->count();
        
        return ['notExistingTrans' => $results, 'count' => $count];
    }
    
    /**
     * returns translation in depend on the sent parameters
     * @param integer $id
     * @param string $labelId
     * @param string $language
     * @return unknown
     */
    public function getTranslation($id, $labelId = '', $language = '')
    {
        $parameters = [];
        
        if ('' == $id) {
            $parameters['label_id']  = $labelId;
            $parameters['language']  = $language;
            
            $where = ' label_id = :label_id AND language = :language';
            
        } else {
            $parameters['id']  = $id;
            
            $where = ' id= :id';
        }
        
        $sql = "SELECT * FROM ht_label_trans WHERE " . $where;
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($parameters);
        $row = $results->current();
        
        return $row;
    }
    
    /**
     * inserts new or update existing translation in depend of send parameters
     * @param unknown $data
     */
    public function saveTranslation($data)
    {
        $parameters = [];
    
        $id       = $data['id'];
        $text     = $data['text'];
    
        $parameters['text'] = $text;
    
        if ($id) {
            //If id is not empty, update existing translation
            $parameters['id'] = $id;
            $sql = "UPDATE ht_label_trans SET text = :text WHERE id = :id";
        } else {
            //If id is empty, insert new translation
            $labelId  = $data['label_id'];
            $language = $data['language'];
            
            $parameters['label_id'] = (int)$labelId;
            $parameters['language'] = $language;
            
            $sql = "INSERT INTO ht_label_trans (label_id, language, text) VALUES (:label_id, :language, :text)";
        }
    
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $statement->execute($parameters);
    }
    
    /**
     * deletes translation in depend on the sent translation id
     * @param unknown $id
     */
    public function deleteTranslation($id)
    {
        $parameters['id'] = $id;
    
        $sql = "DELETE FROM ht_label_trans WHERE id = :id";
    
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $statement->execute($parameters);
    }
}

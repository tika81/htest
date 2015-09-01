<?php
namespace Label\Model;

class LabelModel
{
    private $dbAdapter;
    
    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }
    
    /**
     * returns existing labels in depend on the sent parameters. Needed for datatables
     * @param integer $displayStart
     * @param integer $displayLenght
     * @param string $keyword
     * @param string $order
     * @return unknown
     */
    public function getLabels($displayStart, $displayLenght, $keyword, $order)
    {
        $parameters = [];
        $parameters['limit']  = (int)$displayLenght;
        $parameters['offset'] = (int)$displayStart;
        
        $sql = "SELECT * FROM ht_label ";
        
        //if keyword is not empty
        if ('' != $keyword) {
            $sql .= ' WHERE name = :keyword OR default_text LIKE :likeKeyword ';
            $parameters['keyword']     = $keyword;
            $parameters['likeKeyword'] = '%' . $keyword . '%';
        }
        
        $sql .= ' ORDER BY ' . $order . ' LIMIT :limit OFFSET :offset';
        
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($parameters);
        
        return $results;
    }
    
    /**
     * return label
     * @param integer $id
     * @return unknown
     */
    public function getLabel($id)
    {
        $parameters = [];
        $parameters['id']  = $id;
        
        $sql = "SELECT * FROM ht_label WHERE id= :id";
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($parameters);
        $row = $results->current();
        
        return $row;
    }
    
    /**
     * counts lebels in depend on the sent parameter. Needed for datatables
     * @param string $keyword
     */
    public function countLabels($keyword)
    {
        $parameters = [];
        
        $sql = "SELECT id FROM ht_label ";
        
        if ('' != $keyword) {
            $sql .= ' WHERE name = :keyword OR default_text LIKE :likeKeyword ';
            $parameters['keyword']     = $keyword;
            $parameters['likeKeyword'] = '%' . $keyword . '%';
        }
        
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $results = $statement->execute($parameters);

        return $results->count();
    }
    
    /**
     * inserts new or update existing label in depend of send parameters
     * @param array $data
     */
    public function saveLabel($data)
    {
        $parameters = [];
        
        $id          = $data['id'];
        $name        = $data['name'];
        $defaultText = $data['default_text'];
        
        $parameters['name']         = $name;
        $parameters['default_text'] = $defaultText;
        
        if ($id) {
            //If id is not empty, update label
            $parameters['id'] = $id;
            $sql = "UPDATE ht_label SET name = :name, default_text = :default_text WHERE id = :id";
        } else {
            //If id is empty, insert new label
            $sql = "INSERT INTO ht_label (name, default_text) VALUES (:name, :default_text)";
        }
        
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $statement->execute($parameters);
    }
    
    /**
     * deletes label and all labels translations in depend on the sent label id
     * @param integer $id
     */
    public function deleteLabel($id)
    {
        $parameters['id'] = $id;
        
        $sql = "DELETE l, t 
            FROM ht_label l 
            LEFT JOIN ht_label_trans t ON l.id = t.label_id
            WHERE l.id = :id";
        
        $statement = $this->dbAdapter->createStatement($sql);
        $statement->prepare();
        $statement->execute($parameters);
    }
    
}

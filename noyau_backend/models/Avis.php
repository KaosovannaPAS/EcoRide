<?php
// noyau_backend/models/Avis.php
class Avis
{
    private $mongoDb;
    private $collection;

    public function __construct($mongoDb)
    {
        $this->mongoDb = $mongoDb;
        if ($this->mongoDb) {
            $this->collection = $this->mongoDb->selectCollection('avis');
        }
    }

    public function create($trajet_id, $auteur_id, $cible_id, $note, $commentaire)
    {
        if (!$this->collection)
            return false;

        $document = [
            'trajet_id' => $trajet_id,
            'auteur_id' => $auteur_id,
            'cible_id' => $cible_id,
            'note' => $note,
            'commentaire' => $commentaire,
            'statut' => 'en_attente', // en_attente | approuve | rejete
            'date_creation' => new MongoDB\BSON\UTCDateTime()
        ];

        $result = $this->collection->insertOne($document);
        return $result->getInsertedCount() > 0;
    }

    public function getPending()
    {
        if (!$this->collection)
            return [];
        $cursor = $this->collection->find(['statut' => 'en_attente']);
        return $cursor->toArray();
    }

    public function updateStatus($id, $statut)
    {
        if (!$this->collection)
            return false;

        $result = $this->collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($id)],
        ['$set' => ['statut' => $statut]]
        );
        return $result->getModifiedCount() > 0;
    }

    public function getApprovedByReviewee($cible_id)
    {
        if (!$this->collection)
            return [];
        $cursor = $this->collection->find([
            'cible_id' => $cible_id,
            'statut' => 'approuve'
        ]);
        return $cursor->toArray();
    }
}

<?php
// noyau_backend/models/Review.php
class Review
{
    private $mongoDb;
    private $collection;

    public function __construct($mongoDb)
    {
        $this->mongoDb = $mongoDb;
        if ($this->mongoDb) {
            $this->collection = $this->mongoDb->selectCollection('reviews');
        }
    }

    public function create($trip_id, $reviewer_id, $reviewee_id, $rating, $comment)
    {
        if (!$this->collection)
            return false;

        $document = [
            'trip_id' => $trip_id,
            'reviewer_id' => $reviewer_id,
            'reviewee_id' => $reviewee_id,
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'pending', // pending | approved | rejected
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];

        $result = $this->collection->insertOne($document);
        return $result->getInsertedCount() > 0;
    }

    public function getPending()
    {
        if (!$this->collection)
            return [];
        $cursor = $this->collection->find(['status' => 'pending']);
        return $cursor->toArray();
    }

    public function updateStatus($id, $status)
    {
        if (!$this->collection)
            return false;

        $result = $this->collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($id)],
        ['$set' => ['status' => $status]]
        );
        return $result->getModifiedCount() > 0;
    }

    public function getApprovedByReviewee($reviewee_id)
    {
        if (!$this->collection)
            return [];
        $cursor = $this->collection->find([
            'reviewee_id' => $reviewee_id,
            'status' => 'approved'
        ]);
        return $cursor->toArray();
    }
}

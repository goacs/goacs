<?php

namespace App\Models;

trait DeepClone {
    public function clone(): static {
        $copy = parent::replicate();
        $copy->push();

        foreach ($this->getRelations() as $relation => $entries){
            foreach($entries as $entry){
                $e = $entry->replicate();
                if ($e->push()){
                    $copy->{$relation}()->save($e);
                }
            }
        }

        return $copy;
    }
}

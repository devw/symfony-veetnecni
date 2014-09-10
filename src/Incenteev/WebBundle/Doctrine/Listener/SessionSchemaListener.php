<?php

namespace Incenteev\WebBundle\Doctrine\Listener;

use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Symfony\Bridge\Doctrine\HttpFoundation\DbalSessionHandlerSchema;

/**
 * Doctrine listener adding the schema for the session table.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class SessionSchemaListener
{
    private $schema;

    public function __construct(DbalSessionHandlerSchema $schema)
    {
        $this->schema = $schema;
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $event)
    {
        $this->schema->addToSchema($event->getSchema());
    }
}

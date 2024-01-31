<?php


class ApiController
{
    public function getOrdersCanceled()
    {
        // TODO: deberia validad a traves de un middleware desde donde viene la request, y si tiene el JWT entre otras cosas

        $associatedOrdersCanceled = $_SESSION['associated_orders_canceled'] ? $_SESSION['associated_orders_canceled'] : [];


        header('Content-Type: application/json');
        echo json_encode($associatedOrdersCanceled);
    }

    // TODO: deberia sumar un archivo routes y configurar los endpoints, como por ejemplo este: seria algo asi como GET:  api/get-associated-irders-canceled 
}
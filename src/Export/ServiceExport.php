<?php

namespace App\Export;

use App\Entity\Service;
use App\Service\Export;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class ServiceExport
{
    /**
     * Exporte les données
     */
    public function exportData($services)
    {
        $arrayData[] = array_keys($this->getDatas($services[0]));

        foreach ($services as $service) {
            $arrayData[] = $this->getDatas($service);
        }

        $export = new Export("export_services", "xlsx", $arrayData, null);

        return $export->exportFile();
    }

    /**
     * Retourne les résultats sous forme de tableau
     * @param Service $service
     * @return array
     */
    protected function getDatas(Service $service)
    {
        return [
            "N° service" => $service->getId(),
            "Service" =>  $service->getName(),
            "Pôle" => $service->getPole()->getName(),
            "Téléphone" => $service->getPhone(),
            "Email" => $service->getEmail(),
            "Adresse" => $service->getAddress(),
            "Ville" => $service->getCity(),
            "Date de création" => Date::PHPToExcel($service->getCreatedAt()->format("d/m/Y")),
        ];
    }
}

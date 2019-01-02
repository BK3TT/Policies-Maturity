<?php

class Policies
{
    private $policyTypeManagementFee = array
    (
        "A" => 0.03,
        "B" => 0.05,
        "C" => 0.07
    );
    
    private $policyMaturityData = array();

    protected function getPoliciesData()
    {
        $policiesData = array_map("str_getcsv", file("local_data/MaturityData.csv", FILE_SKIP_EMPTY_LINES));
        $keys = array_shift($policiesData);

        foreach ($policiesData as $i => $row) 
        {
            $policiesData[$i] = array_combine($keys, $row);
        }

        return $policiesData;
    }

    public function viewPolicyData()
    {
        echo json_encode($this->getPoliciesData());
    }

    public function policyMaturity($keys)
    {

        $keys = explode(",", $keys);
        $policiesData = $this->getPoliciesData();

        $counter = 0;

        foreach($keys as $key)
        {
            $discretionaryBonus = $policiesData[$key]["discretionary_bonus"];
            $premiums = $policiesData[$key]["premiums"];
            $uplift = "1." . $policiesData[$key]["uplift_percentage"];

            $managementFee = $this->policyTypeManagementFee[substr($policiesData[$key]["policy_number"], 0, 1)];
            $managementFee = ($premiums * $managementFee);

            $maturityValue = "£" . ($premiums - $managementFee + $discretionaryBonus) * $uplift;

            $this->policyMaturityData[$counter]->policyNumber = $policiesData[$key]["policy_number"];
            $this->policyMaturityData[$counter]->maturityValue = $maturityValue;

            $counter++;
        }

        $this->exportPolicyXML();
    }

    protected function exportPolicyXML()
    {
        $xmlTree = new DOMDocument('1.0', 'UTF-8');

        $xmlRoot = $xmlTree->createElement("XML");
        $xmlRoot = $xmlTree->appendChild($xmlRoot);

        $xmlMaturityData = $xmlTree->createElement("maturityData");
        $xmlMaturityData = $xmlRoot->appendChild($xmlMaturityData);

        foreach($this->policyMaturityData as $key => $value)
        {
            $xmlMaturityData->appendChild($xmlTree->createElement('policyNumber', $value->policyNumber));
            $xmlMaturityData->appendChild($xmlTree->createElement('maturityValue', $value->maturityValue));
        }

        ob_clean();
        flush();

        if($xmlTree->save("file_downloads/xml/policiesMaturity.xml"))
        {
            echo json_encode(array("status" => "success"));
        }
        else 
        {
            echo json_encode(array("status" => "failed"));
        }      
    }
}


?>
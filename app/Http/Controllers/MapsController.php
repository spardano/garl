<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Shapefile\Shapefile;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

class MapsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function shp(Request $req)
    {
        $rules = [
            'files.*'      => 'required'
        ];

        $this->validate($req, $rules);

        $files   = $req->file("files");
        $fs     = [];
        $allValid = true;
        foreach($files as $f){
            $ext    =  $f->getClientOriginalExtension();
            if(!in_array($ext, ["shp", "cpg", "dbf", "prj", "qpj", "shx"])) $allValid = false;
            $fs[] = $f->getClientOriginalName();
        }
        
        if(!$allValid) return response()->json(['errors' => ['file' => ['The file must be a file of type: shp.']], 'The given data was invalid' ], 422);
        
        $shpFiles = [];
        foreach($files as $f){
            $f->move(public_path("files"), $f->getClientOriginalName());
            $shpFiles[$f->getClientOriginalExtension()]  = public_path("files") ."\\". $f->getClientOriginalName();
        }
        try {
            // Open Shapefile
            $Shapefile = new ShapefileReader($shpFiles);
            
            $response   = [];
            $response['geoJson'] = [];
            $response['data']    = [];
            // Read all the records
            while ($Geometry = $Shapefile->fetchRecord()) {
                // Skip the record if marked as "deleted"
                if ($Geometry->isDeleted()) {
                    continue;
                }
                // Print Geometry as GeoJSON
                $response['geoJson'][] = $Geometry->getGeoJSON();
                
                // Print DBF data
                $response['data'][] = $Geometry->getDataArray();
            }
            $response['parameters'] = array_keys($response['data'][0]);
            return response()->json($response);
        } catch (ShapefileException $e) {
            return response()->json(['errors' => ['file' => ['One or more file not exists (Files must be uploaded: .shp,.cpg,.dbf,.prj,.qpj,.shx)']], 'The given data was invalid' ], 422);
        }

        return response()->json([]);
    }

    public function cluster(Request $req){
        $rules = [
            'kabupaten'      => 'required|in:Magetan,Solok',
        ];

        $this->validate($req, $rules);

        $kabName = $req->input("kabupaten");

        $fileName = public_path("files")."\\verifikasi_".strtolower($kabName).".shp";
        if(!file_exists($fileName)) return response()->json(['message' => 'file not exists'], 500);

        try {
            // Open Shapefile
            $Shapefile = new ShapefileReader($fileName);
            
            $response   = [];
            $response['geoJson'] = [];
            $response['data']    = [];
            $response['parameters'] = $this->getParam($kabName);
            $response['table']      = $this->getTableData($kabName);
            $removeField            = [ 'magetan' => ['CLUSTER', 'CLASS'], 'solok' => ['KELAS', 'CLUSTER_1']];

            // Read all the records
            while ($Geometry = $Shapefile->fetchRecord()) {
                // Skip the record if marked as "deleted"
                if ($Geometry->isDeleted()) {
                    continue;
                }
                // Print Geometry as GeoJSON
                $response['geoJson'][] = json_decode($Geometry->getGeoJson());
                
                // Print DBF data
                $data               = $Geometry->getDataArray();
                foreach($data as $k => $d){
                    if($k == 'LUAS_HA') $data[$k] = number_format($d, 2, ",", ".");
                    if(in_array($k, $removeField[strtolower($kabName)])) unset($data[$k]);
                }
                $response['data'][] = $data;
            }
            return response()->json($response);
        } catch (ShapefileException $e) {
            return response()->json(['errors' => ['file' => ['One or more file not exists (Files must be uploaded: .shp,.cpg,.dbf,.prj,.qpj,.shx)']], 'The given data was invalid' ], 422);
        }

        return response()->json([]);
    }

    private function getParam($kabupaten){
        $data = [];
        if(strtolower($kabupaten) == 'magetan'){
            $data = [
                'cluster'   => 'CLUSTER_1',
                // 'class'     => 'CLASS_1'
            ];
        }else{
            $data = [
                'cluster'   => 'CLUSTER_3',
                // 'class'     => 'KELAS_2'
            ];
        }
        return $data;
    }

    public function getTableData($kabupaten){
        $header['cluster']  = ['Cluster Id', 'Tempertur', 'Curah hujan',' Elevasi (mdpl)', 'Kedalaman Tanah', 'Drainase', 'Tekstur Tanah', 'Kemasaman tanah', 'KTK', 'KB', 'Relif'];
        $header['class']    = ['Cluster id', 'S1', 'S2', 'S3'];
        $data           = [];
        if(strtolower($kabupaten) == 'magetan'){
            $data['cluster']  = [
                ['1', '24', '300-350', 'rendah', 'dalam', 'baik', 'halus', 'netral', 'sedang', "sangat tinggi", 'agak landai'],
                ['2', '24-25', '300-400', 'agak rendah', 'dalam', 'baik', 'sedang', 'agak masam', 'rendah', 'sedang', 'curam'],
                ['3', '24', '300-350', 'rendah', 'dalam', 'baik', 'halus', 'netral', 'sedang', "sangat tinggi", 'agak curam']
            ];
            $data['class'] = [
                ['1', '4', '0', '17'],
                ['2', '18', '1', '3'],
                ['3', '1', '0', '4'],
                ['0 (outlier)', '0', '0', '5']
            ];
        }else{
            $data['cluster'] = [
                ['1', '25', '300-350', 'tinggi', 'sedang', 'baik', 'halus', 'masam', 'sedang', 'sedang', 'curam'],
                ['2', '25', '250-300', 'tinggi', 'dalam', 'baik', 'agak halus', 'masam', 'sedang', 'sedang', 'agak curam'],
                ['3', '25', '250-350', 'agak tinggi', 'sangat dalam', 'terhambat', 'halus', 'agak masam', 'sedang', 'tinggi', 'agak curam'],
                ['4', '25', '300-350', 'rendah', 'sangat dalam', 'baik', 'halus', 'agak masam', 'sedang', 'tinggi', 'agak curam'],
                ['5', '25', '300-350', 'tinggi', 'dalam', 'baik', 'halus', 'agak masam', 'tinggi', 'tinggi', 'agak curam'],
                ['6', '25', '300-400', 'tinggi', 'sedang', 'baik', 'halus', 'masam', 'rendah', 'rendah', 'sangat curam'],
                ['7', '25', '300-350', 'rendah', 'dalam', 'baik', 'halus', 'masam', 'rendah', 'rendah', 'agak curam'],
                ['8', '25', '300-350', 'tinggi', 'sangat dalam', 'baik', 'agak halus', 'agak masam', 'rendah', 'rendah', 'curam'],
                ['9', '25', '250-300', 'tinggi', 'dangkal', 'baik', 'halus', 'masam', 'sedang', 'sedang', 'sangat curam'],
                ['10', '25', '300-350', 'agak tinggi', 'dalam', 'terhambat', 'halus', 'agak masam', 'sedang', 'tinggi', 'agak curam'],
            ];
            $data['class'] = [
                ['1', '0', '5', '5'],
                ['2', '0', '2', '11'],
                ['3', '0', '2', '1'],
                ['4', '0', '2', '7'],
                ['5', '0', '17', '6'],
                ['6', '0', '0', '6'],
                ['7', '0', '1', '11'],
                ['8', '0', '4', '0'],
                ['9', '0', '1', '2'],
                ['10', '0', '0', '12'],
                ['0 (outlier)', '2', '6', '22'],
            ];
        }
        return ['header' => $header, 'data' => $data];
    }

    public function  maps(Request $req){
        $fileName = public_path("files")."\\klbp_model.shp";
        if(!file_exists($fileName)) return response()->json(['message' => 'file not exists'], 500);

        try {
            // Open Shapefile
            $Shapefile = new ShapefileReader($fileName);
            
            $response   = [];
            $response['geoJson'] = [];
            $response['data']    = [];

            // Read all the records
            while ($Geometry = $Shapefile->fetchRecord()) {
                // Skip the record if marked as "deleted"
                if ($Geometry->isDeleted()) {
                    continue;
                }
                // Print Geometry as GeoJSON
                $response['geoJson'][] = json_decode($Geometry->getGeoJson());
                
                // Print DBF data
                $data               = $Geometry->getDataArray();
                $response['data'][] = $data;
                $response['class']  = "CLASS_ATTR";
            }
            return response()->json($response);
        } catch (ShapefileException $e) {
            return response()->json(['errors' => ['file' => ['One or more file not exists (Files must be exists: .shp,.cpg,.dbf,.prj,.qpj,.shx)']], 'The given data was invalid' ], 422);
        }

        return response()->json([]);
    }
}

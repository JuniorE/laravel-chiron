<?php
    /**
     * Created by PhpStorm.
     * User: JuniorE.
     * Date: 10/10/2020
     * Time: 13:13
     */

    namespace Juniore\Chiron;

    use Carbon\Carbon;
    use Illuminate\Http\Client\PendingRequest;
    use Illuminate\Http\Client\Response;
    use Illuminate\Support\Facades\Http;


    class Chiron
    {
        private PendingRequest $http;

        public function __construct()
        {
            $oauth_response = $this->getAccessToken();
            $this->http = Http::baseUrl(config('chiron.api_base_url'))->withToken($oauth_response['access_token']);
        }

        public function makeReservation($trip_number, $company_number, $company_name): array
        {
            $requestBody = [
                'status'           => 'reservatie',
                'ritnummer'        => $trip_number,
                'rit'              => [
                    'taxibedrijf' => [
                        'aanbieder' => [
                            'registratie' => $company_number,
                            'naam'        => $company_name
                        ]
                    ]
                ],
                'broncreatiedatum' => Carbon::now()->toIso8601ZuluString('second')
            ];
            $responseBody = $this->http->post('/chiron/taxirit', $requestBody)->json();
            return [
                'requestBody'  => $requestBody,
                'responseBody' => $responseBody
            ];
        }

        public function sendDepartureStatus(
            $previousStatusRequest,
            $driver_licence_number,
            $license_plate,
            $departured_at,
            $pickup_longitude,
            $pickup_latitude
        ) {
            $requestBody = $previousStatusRequest;
            $requestBody['status'] = 'vertrek';
            $requestBody['rit']['voertuig'] = [
                'nummerplaat' => $license_plate
            ];
            $requestBody['rit']['uitvoerder'] = [
                'bestuurderspasnummer' => $driver_licence_number
            ];
            $requestBody['rit']['vertrektijdstip'] = $departured_at;
            $requestBody['rit']['vertrekpunt'] = [
                'lengtegraad'  => $pickup_longitude,
                'breedtegraad' => $pickup_latitude
            ];
            $responseBody = $this->http->post('/chiron/taxirit', $requestBody)->json();

            info(json_encode($responseBody, JSON_PRETTY_PRINT));

            return [
                'requestBody'  => $requestBody,
                'responseBody' => $responseBody
            ];
        }

        public function sendArrivalStatus(
            $previousStatusRequest,
            $price,
            $distance,
            $arrived_at,
            $destination_longitude,
            $destination_latitude
        ): array {
            $requestBody = $previousStatusRequest;
            $requestBody['status'] = 'aankomst';
            $requestBody['rit']['aankomsttijdstip'] = $arrived_at;
            $requestBody['rit']['aankomstpunt'] = [
                'lengtegraad'  => $destination_longitude,
                'breedtegraad' => $destination_latitude
            ];
            $requestBody['rit']['afstand'] = [
                'waarde' => $distance
            ];
            $requestBody['rit']['kostprijs'] = [
                'waarde' => $price
            ];
            $responseBody = $this->http->post('/chiron/taxirit', $requestBody)->json();
            info(json_encode($responseBody, JSON_PRETTY_PRINT));
            return [
                'requestBody' => $requestBody,
                'responseBody' => $responseBody
            ];
        }

        /**
         * @return Response
         */
        private function getAccessToken(): Response
        {
            info('getAccessToken');
            return Http::asForm()
                ->baseUrl(config('chiron.api_base_url'))
                ->withBasicAuth(config('chiron.client_id'), config('chiron.client_secret'))
                ->post('/oauth/token',
                    ['grant_type' => 'client_credentials']
                );
        }


    }

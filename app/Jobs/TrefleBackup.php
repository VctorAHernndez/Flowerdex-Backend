<?php

namespace App\Jobs;

use App\Models\DivisionClass;
use App\Models\DivisionFamily;
use App\Models\DivisionGenus;
use App\Models\DivisionKingdom;
use App\Models\DivisionOrder;
use App\Models\DivisionPhylum;
use App\Models\DivisionSpecies;
use App\Models\DivisionSubkingdom;
use App\Services\TrefleClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrefleBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private TrefleClient $api;
    private const PAGE_SIZE = 20;
    private const INTER_API_CALL_SLEEP_IN_SECONDS = 0.5;
     
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->api = new TrefleClient;
    }

    /**
     * Backs up Kingdoms from Trefle's API.
     */
    private function handleKingdomBackup(): void
    {
        $currentPage = 1;

        do {
            $json = $this->api->getKingdoms($currentPage);

            $totalResults = $json['meta']['total'];

            foreach($json['data'] as $kingdomData) {
                $kingdom = DivisionKingdom::where(['trefle_id' => $kingdomData['id']])->first();

                if (!$kingdom) {
                    $kingdom = new DivisionKingdom;
                }

                $kingdom->name = $kingdomData['name'];
                $kingdom->slug = $kingdomData['slug'];
                $kingdom->trefle_id = $kingdomData['id'];
                $kingdom->save();
            }

            $currentPage += 1;

        } while (($currentPage - 1) * TrefleBackup::PAGE_SIZE < $totalResults);
    }

    /**
     * Backs up Subkingdoms from Trefle's API.
     */
    private function handleSubkingdomBackup(): void
    {
        $currentPage = 1;

        do {
            $json = $this->api->getSubkingdoms($currentPage);

            $totalResults = $json['meta']['total'];

            foreach($json['data'] as $data) {
                $subkingdom = DivisionSubkingdom::where(['trefle_id' => $data['id']])->first();

                if (!$subkingdom) {
                    $subkingdom = new DivisionSubkingdom;
                }

                $subkingdom->name = $data['name'];
                $subkingdom->slug = $data['slug'];
                $subkingdom->trefle_id = $data['id'];

                $kingdom = DivisionKingdom::where('trefle_id', $data['kingdom']['id'])->first();
                $subkingdom->division_kingdom_id = $kingdom->id;

                $subkingdom->save();
            }

            $currentPage += 1;
        } while (($currentPage - 1) * TrefleBackup::PAGE_SIZE < $totalResults);
    }

    /**
     * Backs up Phylums from Trefle's API.
     */
    private function handlePhylumBackup(): void
    {
        $currentPage = 1;

        do {
            $json = $this->api->getPhylums($currentPage);

            $totalResults = $json['meta']['total'];

            foreach($json['data'] as $data) {
                $phylum = DivisionPhylum::where(['trefle_id' => $data['id']])->first();

                if (!$phylum) {
                    $phylum = new DivisionPhylum;
                }

                $phylum->name = $data['name'];
                $phylum->slug = $data['slug'];
                $phylum->trefle_id = $data['id'];

                $subkingdom = DivisionSubkingdom::where('trefle_id', $data['subkingdom']['id'])->first();
                $phylum->division_subkingdom_id = $subkingdom->id;

                $phylum->save();
            }

            $currentPage += 1;
        } while (($currentPage - 1) * TrefleBackup::PAGE_SIZE < $totalResults);
    }

    /**
     * Backs up Classes from Trefle's API.
     */
    private function handleClassBackup(): void
    {
        $currentPage = 1;

        do {
            $json = $this->api->getClasses($currentPage);

            $totalResults = $json['meta']['total'];

            foreach($json['data'] as $data) {
                $class = DivisionClass::where(['trefle_id' => $data['id']])->first();

                if (!$class) {
                    $class = new DivisionClass;
                }

                $class->name = $data['name'];
                $class->slug = $data['slug'];
                $class->trefle_id = $data['id'];

                $phylum = DivisionPhylum::where('trefle_id', $data['division']['id'])->first();
                $class->division_phylum_id = $phylum->id;

                $class->save();
            }

            $currentPage += 1;
        } while (($currentPage - 1) * TrefleBackup::PAGE_SIZE < $totalResults);
    }

    /**
     * Backs up Orders from Trefle's API.
     */
    private function handleOrderBackup(int $startingPage = 1): void
    {
        $currentPage = $startingPage;

        do {
            $json = $this->api->getOrders($currentPage);

            $totalResults = $json['meta']['total'];

            foreach($json['data'] as $data) {
                $order = DivisionOrder::where(['trefle_id' => $data['id']])->first();

                if (!$order) {
                    $order = new DivisionOrder;
                }

                $order->name = $data['name'];
                $order->slug = $data['slug'];
                $order->trefle_id = $data['id'];

                $class = DivisionClass::where('trefle_id', $data['division_class']['id'])->first();
                $order->division_class_id = $class->id;

                $order->save();
            }

            $currentPage += 1;
        } while (($currentPage - 1) * TrefleBackup::PAGE_SIZE < $totalResults);
    }

    /**
     * Backs up Families from Trefle's API.
     */
    private function handleFamilyBackup(int $startingPage = 1): void
    {
        $currentPage = $startingPage;

        do {
            $json = $this->api->getFamilies($currentPage);

            $totalResults = $json['meta']['total'];

            foreach($json['data'] as $data) {
                $family = DivisionFamily::where(['trefle_id' => $data['id']])->first();

                if (!$family) {
                    $family = new DivisionFamily;
                }

                $family->common_name = $data['common_name'];
                $family->name = $data['name'];
                $family->slug = $data['slug'];
                $family->trefle_id = $data['id'];

                // Some Families have no Order in the API
                if ($data['division_order'] !== NULL) {
                    $order = DivisionOrder::where('trefle_id', $data['division_order']['id'])->first();
                    $family->division_order_id = $order->id;
                } else {
                    $family->division_order_id = NULL;
                }

                $family->save();
            }

            $currentPage += 1;
        } while (($currentPage - 1) * TrefleBackup::PAGE_SIZE < $totalResults);
    }

    /**
     * Backs up Genera from Trefle's API.
     */
    private function handleGenusBackup(int $startingPage = 1): void
    {
        $currentPage = $startingPage;

        do {
            $json = $this->api->getGenera($currentPage);

            $totalResults = $json['meta']['total'];

            foreach($json['data'] as $data) {
                $genus = DivisionGenus::where(['trefle_id' => $data['id']])->first();

                if (!$genus) {
                    $genus = new DivisionGenus;
                }

                $genus->name = $data['name'];
                $genus->slug = $data['slug'];
                $genus->trefle_id = $data['id'];

                // Some Genera have no Family in the API
                if ($data['family'] !== NULL) {
                    $family = DivisionFamily::where('trefle_id', $data['family']['id'])->first();
                    $genus->division_family_id = $family->id;
                } else {
                    $genus->division_family_id = NULL;
                }

                $genus->save();
            }

            // Sleep before making another network call
            sleep(TrefleBackup::INTER_API_CALL_SLEEP_IN_SECONDS);

            $currentPage += 1;
        } while (($currentPage - 1) * TrefleBackup::PAGE_SIZE < $totalResults);
    }

    /**
     * Backs up Species from Trefle's API.
     */
    private function handleSpeciesBackup(int $startingPage = 1): void
    {
        $currentPage = $startingPage;

        do {
            $json = $this->api->getSpecies($currentPage);

            $totalResults = $json['meta']['total'];

            foreach($json['data'] as $data) {
                $species = DivisionSpecies::where(['trefle_id' => $data['id']])->first();

                if (!$species) {
                    $species = new DivisionSpecies;
                }

                $species->common_name = $data['common_name'];
                $species->slug = $data['slug'];
                $species->scientific_name = $data['scientific_name'];
                $species->year = $data['year'];
                $species->image_url = $data['image_url'];
                $species->synonyms = json_encode($data['synonyms']);
                $species->trefle_id = $data['id'];

                // Some Species have no Genus in the API
                // if ($data['genus_id'] !== NULL) {
                $genus = DivisionGenus::where('trefle_id', $data['genus_id'])->first();
                $species->division_genus_id = $genus->id;
                // } else {
                //     $species->division_genus_id = NULL;
                // }

                $species->save();
            }

            // Sleep before making another network call
            sleep(TrefleBackup::INTER_API_CALL_SLEEP_IN_SECONDS);

            $currentPage += 1;
        } while (($currentPage - 1) * TrefleBackup::PAGE_SIZE < $totalResults);
    }

    /**
     * Execute the job.
     */
    // NOTE: this do-while approach might fail in the case where $totalResults changes
    // TODO: spit out logs when each step has success
    // TODO: each of these functions could be handled in succession as separate tasks, instead of under a single process
    // TODO: increase timeout in genus? actually decrease and make each request retryable by handling ConnectionException
    public function handle(): void
    {
        // TODO: should we record each time a FK changes?
        // TODO: should we record each time an entity is deleted from trefle?
        // TODO: can we confirm the json has the expected payload using serializers at the TrefleClient level?

        $this->handleKingdomBackup(); // 1
        $this->handleSubkingdomBackup(); // 1
        $this->handlePhylumBackup(); // 9
        $this->handleClassBackup(); // 10
        $this->handleOrderBackup(); // 93
        $this->handleFamilyBackup(); // 683 total -> 6 (where division_order_id is not null)
        $this->handleGenusBackup(); // 16508 -> 484 (where division_family_id is null)
        $this->handleSpeciesBackup(3572); // 489358
    }
}

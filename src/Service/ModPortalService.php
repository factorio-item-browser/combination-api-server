<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use BluePsyduck\FactorioModPortalClient\Client\ClientInterface;
use BluePsyduck\FactorioModPortalClient\Entity\Mod;
use BluePsyduck\FactorioModPortalClient\Entity\Release;
use BluePsyduck\FactorioModPortalClient\Entity\Version;
use BluePsyduck\FactorioModPortalClient\Exception\ClientException;
use BluePsyduck\FactorioModPortalClient\Request\FullModRequest;
use BluePsyduck\FactorioModPortalClient\Utils\ModUtils;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;

/**
 * The service handling the access to the Factorio Mod Portal.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModPortalService
{
    private ClientInterface $modPortalClient;

    public function __construct(ClientInterface $modPortalClient)
    {
        $this->modPortalClient = $modPortalClient;
    }

    /**
     * Requests the mods from the Mod Portal API. Not known mods will be missing in the result array.
     * @param array<string> $modNames
     * @return array<string, Mod>
     */
    public function requestMods(array $modNames): array
    {
        $promises = [];
        try {
            foreach ($modNames as $modName) {
                $request = new FullModRequest();
                $request->setName($modName);
                $promises[$modName] = $this->modPortalClient->sendRequest($request);
            }
        } catch (ClientException $e) {
        }

        $mods = [];
        $responses = Utils::settle($promises)->wait();
        foreach ($responses as $response) {
            if ($response['state'] === PromiseInterface::FULFILLED) {
                /** @var Mod $mod */
                $mod = $response['value'];
                $mods[$mod->getName()] = $mod;
            }
        }
        return $mods;
    }

    /**
     * Selects the latest release for each of the mod, which is compatible to the specified Factorio version.
     * @param array<string, Mod> $mods
     * @param Version|null $factorioVersion
     * @return array<string, Release>
     */
    public function selectLatestReleases(array $mods, ?Version $factorioVersion = null): array
    {
        $releases = [];
        foreach ($mods as $name => $mod) {
            $release = ModUtils::selectLatestRelease($mod, $factorioVersion);
            if ($release !== null) {
                $releases[$name] = $release;
            }
        }
        return $releases;
    }
}

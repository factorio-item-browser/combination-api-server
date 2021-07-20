<?php

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Service;

use BluePsyduck\FactorioModPortalClient\Client\ClientInterface;
use BluePsyduck\FactorioModPortalClient\Entity\Mod;
use BluePsyduck\FactorioModPortalClient\Entity\Release;
use BluePsyduck\FactorioModPortalClient\Entity\Version;
use BluePsyduck\FactorioModPortalClient\Exception\ClientException;
use BluePsyduck\FactorioModPortalClient\Exception\ErrorResponseException;
use BluePsyduck\FactorioModPortalClient\Request\FullModRequest;
use BluePsyduck\FactorioModPortalClient\Utils\ModUtils;
use FactorioItemBrowser\CombinationApi\Server\Exception\FailedModPortalRequestException;
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
     * @throws FailedModPortalRequestException
     */
    public function requestMods(array $modNames): array
    {
        $mods = [];
        $promises = [];
        try {
            foreach ($modNames as $modName) {
                $request = new FullModRequest();
                $request->setName($modName);

                $promises[] = $this->modPortalClient->sendRequest($request)->then(
                    function (Mod $mod) use (&$mods): void {
                        $mods[$mod->getName()] = $mod;
                    },
                    function (ClientException $exception): void {
                        // Ignore mods not existing on the mod portal.
                        if ($exception instanceof ErrorResponseException && $exception->getCode() === 404) {
                            return;
                        }
                        throw new FailedModPortalRequestException($exception->getMessage(), $exception);
                    },
                );
            }
        } catch (ClientException $e) {
            throw new FailedModPortalRequestException($e->getMessage(), $e);
        }

        Utils::all($promises)->wait();
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

<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Composer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;
use Symfony\Component\Finder\Finder;

class PluginsInstaller extends LibraryInstaller
{
    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $targetDir = $package->getTargetDir();
        return 'plugins'. ($targetDir ? '/'.$targetDir : '');
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'newscoop-plugin' === $packageType;
    }


    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);

        file_put_contents(
            $this->vendorDir. '/../plugins/cache/add_'.str_replace('/', '-', $package->getName()).'_package.json', 
            json_encode($this->preparePackageMeta($package))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::uninstall($repo, $package);

        file_put_contents(
            $this->vendorDir. '/../plugins/cache/uninstall_'.str_replace('/', '-', $package->getName()).'_package.json', 
            json_encode($this->preparePackageMeta($package))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        parent::update($repo, $initial, $target);

        file_put_contents(
            $this->vendorDir. '/../plugins/cache/update_'.str_replace('/', '-', $initial->getName()).'_package.json', 
            json_encode(array(
                'initial' => $this->preparePackageMeta($initial),
                'target' => $this->preparePackageMeta($target)
            ))
        );
    }

    private function preparePackageMeta($package)
    {
        return array(
            'version' => $package->getVersion(),
            'name' => $package->getName(),
            'id' => $package->getId(),
            'description' => $package->getDescription(),
            'authors' => $package->getAuthors(),
            'release_date' => $package->getReleaseDate(),
            'license' => $package->getLicense(),
            'targetDir' => $package->getTargetDir()
        );
    }
}

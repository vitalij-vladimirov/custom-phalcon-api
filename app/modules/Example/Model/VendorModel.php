<?php
declare(strict_types=1);

namespace Example\Model;

use Common\BaseClasses\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $lib_name
 * @property string $lib_url
 * @property string $version
 * @property string $environment
 * @property string $description
 *
 * @method static Builder|VendorModel whereLibName($value)
 * @method static Builder|VendorModel whereLibUrl($value)
 * @method static Builder|VendorModel whereEnvironment($value)
 * @method static Builder|VendorModel whereDescription($value)
 *
 * @mixin Model
 */
class VendorModel extends BaseModel
{
    protected $fillable = [
        'lib_name',
        'lib_url',
        'version',
        'environment',
        'description',
    ];

    public function getLibName(): string
    {
        return $this->lib_name;
    }

    public function setLibName(string $lib_name): VendorModel
    {
        $this->lib_name = $lib_name;
        return $this;
    }

    public function getLibUrl(): string
    {
        return $this->lib_url;
    }

    public function setLibUrl(string $lib_url): VendorModel
    {
        $this->lib_url = $lib_url;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): VendorModel
    {
        $this->version = $version;
        return $this;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function setEnvironment(string $environment): VendorModel
    {
        $this->environment = $environment;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): VendorModel
    {
        $this->description = $description;
        return $this;
    }
}

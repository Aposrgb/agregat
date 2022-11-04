<?php

namespace App\Helper\Filter;

use App\Helper\EnumRoles\UserRoles;
use App\Helper\EnumStatus\UserStatus;
use App\Service\ValidatorService;
use Symfony\Component\Validator\Constraints as Assert;

class UserFilter
{
    #[Assert\Valid(groups: ['filter'])]
    protected ?PaginationFilter $pagination;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateArrayInteger'], groups: ['filter'])]
    protected ?string $region = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['filter'])]
    #[Assert\Choice(callback: [UserStatus::class, 'getStatuses'], groups: ['filter'])]
    protected ?string $status = null;

    #[Assert\Choice(callback: [UserRoles::class, 'getRoles'], groups: ['filter'])]
    protected ?string $role = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateArrayInteger'], groups: ['filter'])]
    protected ?string $specialization = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateBoolean'], groups: ['filter'])]
    protected ?bool $participatedInEvent = null;

    protected ?string $belongProgram = null;

    protected ?string $fio = null;

    protected ?string $phone = null;

    protected ?string $email = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateBoolean'], groups: ['filter'])]
    protected string|null|bool $memberAssociation = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateBoolean'], groups: ['filter'])]
    protected string|null|bool $graduateStudent = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateArrayInteger'], groups: ['filter'])]
    protected $profInterest = null;

    #[Assert\Callback([ValidatorService::class, "validateDate"], groups: ['filter'])]
    protected ?string $date = null;

    #[Assert\Callback(callback: [ValidatorService::class, 'validateInteger'], groups: ['filter'])]
    protected ?string $event = null;

    public function __construct()
    {
        $this->pagination = new PaginationFilter();
    }

    /**
     * @return PaginationFilter
     */
    public function getPagination(): PaginationFilter
    {
        return $this->pagination;
    }

    /**
     * @param PaginationFilter $pagination
     * @return UserFilter
     */
    public function setPagination(PaginationFilter $pagination): UserFilter
    {
        $this->pagination = $pagination;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string|null $region
     * @return UserFilter
     */
    public function setRegion(?string $region): UserFilter
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return UserFilter
     */
    public function setStatus(?string $status): UserFilter
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string|null $role
     * @return UserFilter
     */
    public function setRole(?string $role): UserFilter
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    /**
     * @param string|null $specialization
     * @return UserFilter
     */
    public function setSpecialization(?string $specialization): UserFilter
    {
        $this->specialization = $specialization;
        return $this;
    }

    public function getParticipatedInEvent(): ?bool
    {
        return $this->participatedInEvent == "true";
    }

    /**
     * @param string|null $participatedInEvent
     * @return UserFilter
     */
    public function setParticipatedInEvent(?string $participatedInEvent): UserFilter
    {
        if ($participatedInEvent == "false") {
            $this->participatedInEvent = false;
        } else if ($participatedInEvent == "true") {
            $this->participatedInEvent = true;
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBelongProgram(): ?string
    {
        return $this->belongProgram;
    }

    /**
     * @param string|null $belongProgram
     * @return UserFilter
     */
    public function setBelongProgram(?string $belongProgram): UserFilter
    {
        $this->belongProgram = $belongProgram;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFio(): ?string
    {
        return $this->fio;
    }

    /**
     * @param string|null $fio
     * @return UserFilter
     */
    public function setFio(?string $fio): UserFilter
    {
        $this->fio = $fio;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return UserFilter
     */
    public function setPhone(?string $phone): UserFilter
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return UserFilter
     */
    public function setEmail(?string $email): UserFilter
    {
        $this->email = $email;
        return $this;
    }

    public function getMemberAssociation(): ?bool
    {
        return $this->memberAssociation == "true";
    }

    public function setMemberAssociation(?string $memberAssociation): UserFilter
    {
        $this->memberAssociation = $memberAssociation;
        return $this;
    }

    public function getGraduateStudent(): ?bool
    {
        return $this->graduateStudent == "true";
    }

    public function setGraduateStudent(?string $graduateStudent): UserFilter
    {
        $this->graduateStudent = $graduateStudent;
        return $this;
    }

    public function getProfInterest()
    {
        return $this->profInterest;
    }

    public function setProfInterest($profInterest): UserFilter
    {
        $this->profInterest = $profInterest;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @param string|null $date
     * @return UserFilter
     */
    public function setDate(?string $date): UserFilter
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEvent(): ?string
    {
        return $this->event;
    }

    /**
     * @param string|null $event
     * @return UserFilter
     */
    public function setEvent(?string $event): UserFilter
    {
        $this->event = $event;
        return $this;
    }

}
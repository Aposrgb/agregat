<?php

namespace App\Helper\DTO;

use App\Service\ValidatorService;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    /** @OA\Property(type="string") */
    #[Assert\NotBlank(message: 'Не должно быть пустым', groups: ['registration', 'authorization'])]
    #[Assert\Email(message: 'Не валидная почта', groups: ['registration', 'authorization', 'edit_user'])]
    #[Assert\Length(min: 6, max: 30, minMessage: 'Длина имени почты быть минимум 6 символа', maxMessage: 'Длина почты должна быть максимум 30 символов', groups: ['registration', 'authorization', 'edit_user'])]
    #[Assert\Type(type: 'string', message: 'Неверный тип', groups: ['registration', 'authorization', 'edit_user'])]
    #[Groups(groups: ['registration', 'authorization', 'edit_user'])]
    private $email;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(message: 'Не должно быть пустым', groups: ['registration', 'authorization'])]
    #[Assert\Length(min: 6, max: 30, minMessage: 'Длина пароля должна быть минимум 6 символа', maxMessage: 'Длина пароля должна быть максимум 30 символов', groups: ['registration', 'authorization'])]
    #[Assert\Type(type: 'string', message: 'Неверный тип',  groups: ['registration', 'authorization'])]
    #[Groups(groups: ['registration', 'authorization'])]
    private $password;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(message: 'Не должно быть пустым', groups: ['registration'])]
    #[Assert\Length(min: 2, max: 20, minMessage: 'Длина имени должна быть минимум 2 символа', maxMessage: 'Длина имени должна быть максимум 20 символов', groups: ['registration', 'edit_user'])]
    #[Assert\Type(type: 'string', groups: ['registration', 'edit_user'])]
    #[Groups(groups: ['registration', 'edit_user'])]
    private $name = null;

    /** @OA\Property(type="boolean") */
    #[Assert\NotBlank(message: 'Не должно быть пустым', groups: ['registration'])]
    #[Assert\Type(type: 'boolean', groups: ['registration', 'edit_user'])]
    #[Groups(groups: ['registration', 'edit_user'])]
    private $isJuristic = null;

    /** @OA\Property(type="string") */
    #[Assert\NotBlank(message: 'Не должно быть пустым',groups: ['registration'])]
    #[Assert\Length(min: 2, max: 20, minMessage: 'Длина фамилии должна быть минимум 2 символа', maxMessage: 'Длина фамилии должна быть максимум 20 символов', groups: ['registration', 'edit_user'])]
    #[Assert\Type(type: 'string', groups: ['registration', 'edit_user'])]
    #[Groups(groups: ['registration', 'edit_user'])]
    private $surname = null;

    /** @OA\Property(type="string") */
    #[Assert\Length(min: 2, max: 20, minMessage: 'Длина отчества должна быть минимум 2 символа', maxMessage: 'Длина отчества должна быть максимум 20 символов', groups: ['registration', 'edit_user'])]
    #[Assert\Type(type: 'string', groups: ['registration', 'edit_user'])]
    #[Groups(groups: ['registration', 'edit_user'])]
    private $patronymic = null;

    /** @OA\Property(type="string") */
    #[Assert\Length(min: 5, max: 20, minMessage: 'Длина телефона должна быть минимум 5 символов', maxMessage: 'Длина телефона должна быть максимум 20 символов', groups: ['registration', 'edit_user'])]
    #[Assert\Type(type: 'string', groups: ['registration', 'edit_user'])]
    #[Groups(groups: ['registration', 'edit_user'])]
    private $phone;

    /** @OA\Property(type="string") */
    #[Assert\Type(type: 'string', groups: ['edit_user'])]
    #[Groups(groups: ['edit_user'])]
    private $country = null;

    /** @OA\Property(type="string") */
    #[Assert\Type(type: 'string', groups: ['edit_user'])]
    #[Groups(groups: ['edit_user'])]
    private $city = null;

    /** @OA\Property(type="string") */
    #[Assert\Type(type: 'string', groups: ['edit_user'])]
    #[Groups(groups: ['edit_user'])]
    private $locality = null;

    /** @OA\Property(type="string") */
    #[Assert\Type(type: 'string', groups: ['edit_user'])]
    #[Groups(groups: ['edit_user'])]
    private $index = null;

    /** @OA\Property(type="string") */
    #[Assert\Type(type: 'string', groups: ['edit_user'])]
    #[Groups(groups: ['edit_user'])]
    private $address = null;

    /** @OA\Property(type="datetime", example="2023-01-18 22:13:10") */
    #[Assert\Callback(callback: [ValidatorService::class, 'validateDate'], groups: ['edit_user'])]
    #[Groups(groups: ['edit_user'])]
    private $dateBirth = null;

    /** @OA\Property(type="string") */
    #[Assert\Type(type: 'string', groups: ['edit_user'])]
    #[Groups(groups: ['edit_user'])]
    private $inn  = null;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return UserDTO
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return UserDTO
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     * @return UserDTO
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param null $surname
     * @return UserDTO
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return UserDTO
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return null
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * @param null $patronymic
     * @return UserDTO
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;
        return $this;
    }

    /**
     * @return null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param null $country
     * @return UserDTO
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param null $city
     * @return UserDTO
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return null
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param null $locality
     * @return UserDTO
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
        return $this;
    }

    /**
     * @return null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param null $index
     * @return UserDTO
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param null $address
     * @return UserDTO
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return null
     */
    public function getDateBirth()
    {
        return $this->dateBirth;
    }

    /**
     * @param null $dateBirth
     * @return UserDTO
     */
    public function setDateBirth($dateBirth)
    {
        $this->dateBirth = $dateBirth;
        return $this;
    }

    /**
     * @return null
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * @param null $inn
     * @return UserDTO
     */
    public function setInn($inn)
    {
        $this->inn = $inn;
        return $this;
    }

    public function getIsJuristic()
    {
        return $this->isJuristic;
    }
    public function setIsJuristic($isJuristic)
    {
        $this->isJuristic = $isJuristic;
        return $this;
    }

}
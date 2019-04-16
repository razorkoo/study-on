<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\LessonRepository")
 */
class Lesson
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Course", inversedBy="lessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lessonCourse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleLesson;

    /**
     * @ORM\Column(type="text")
     */
    private $contentLesson;

    /**
     * @ORM\Column(type="integer")
     * @Assert\LessThan(value=10000)
     * @Assert\NotBlank
     */
    private $serialNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLessonCourse(): ?Course
    {
        return $this->lessonCourse;
    }

    public function setLessonCourse(?Course $lessonCourse): self
    {
        $this->lessonCourse = $lessonCourse;

        return $this;
    }

    public function getTitleLesson(): ?string
    {
        return $this->titleLesson;
    }

    public function setTitleLesson(string $titleLesson): self
    {
        $this->titleLesson = $titleLesson;

        return $this;
    }

    public function getContentLesson(): ?string
    {
        return $this->contentLesson;
    }

    public function setContentLesson(string $contentLesson): self
    {
        $this->contentLesson = $contentLesson;

        return $this;
    }

    public function getSerialNumber(): ?int
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(int $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }
}

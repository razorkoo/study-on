<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CourseRepository")
 */
class Course
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleCourse;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $descriptionCourse;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lesson", mappedBy="lessonCourse", cascade={"persist", "remove"})
     * @ORM\OrderBy({"serialNumber"="ASC"})
     *
     */
    private $lessons;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleCourse(): ?string
    {
        return $this->titleCourse;
    }

    public function setTitleCourse(string $titleCourse): self
    {
        $this->titleCourse = $titleCourse;

        return $this;
    }

    public function getDescriptionCourse(): ?string
    {
        return $this->descriptionCourse;
    }

    public function setDescriptionCourse(string $descriptionCourse): self
    {
        $this->descriptionCourse = $descriptionCourse;

        return $this;
    }

    /**
     * @return Collection|Lesson[]
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setLessonCourse($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->contains($lesson)) {
            $this->lessons->removeElement($lesson);
            // set the owning side to null (unless already changed)
            if ($lesson->getLessonCourse() === $this) {
                $lesson->setLessonCourse(null);
            }
        }

        return $this;
    }
}

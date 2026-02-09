<?php 
// src/Domain/Game/State/FigureArea.php
namespace App\Domain\Game\State;

enum FigureArea: string {
    case HOME = 'home';
    case BOARD = 'board';
    case GOAL = 'goal';
}
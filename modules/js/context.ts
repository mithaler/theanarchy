import { writable } from "svelte/store";

export interface CheckedBoxes {
  [key: string]: number[];
}

export interface AnarchyPlayer extends Player {
  tent: number;

  // castle
  gate: number;
  moat: number;
  leftWall: number;
  rightWall: number;
  bottomWall: number;
  topWall: number;
  towerLeftWop: number;
  towerLeftBottom: number;
  towerRightTop: number;
  towerRightBottom: number;

  // pieces
  serfs: number;
  craftsmen: number;
  soldiers: number;
  patrons: number;
  knights: number;
  materials: number;
  silver: number;
  food: number;

  checkedBoxes: {[key: number]: CheckedBoxes}
}

export interface AnarchyData extends Gamedatas<AnarchyPlayer> {
  round: number;
}

export const ctx = writable<AnarchyData>();

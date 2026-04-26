import { writable } from "svelte/store";

export type AnarchyBga = Bga<AnarchyPlayer, AnarchyData>;

export interface BoxSet {
  // section -> ids
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

  availableBoxes?: BoxSet;
  checkedBoxes: BoxSet;
}

export interface AnarchyData extends Gamedatas<AnarchyPlayer> {
  round: number;
}

export interface AnarchyContext {
  data: AnarchyData;
  bga: AnarchyBga;
}

export const ctx = writable<AnarchyContext>();

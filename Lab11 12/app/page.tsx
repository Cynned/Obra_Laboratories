"use client";

import { useState, useEffect } from "react";

const ALL_POKEMON_IDS = Array.from({ length: 1010 }, (_, i) => i + 1);

const typeColors: { [key: string]: string } = {
  grass: "#81d359",
  poison: "#ca74ca",
  fire: "#ec5646",
  rock: "#5c4c06",
  water: "#20b7e4",
  ice: "#96e9e9",
  bug: "#013f05",
  ground: "#aaa45c",
  flying: "#16d3ba",
  electric: "#F8D030",
  normal: "#72805e",
  fairy: "#EE99AC",
  psychic: "#F85888",
  dark: "#705848",
  steel: "#B8B8D0",
  dragon: "#7038F8",
  ghost: "#705898",
  fighting: "#C03028",
};

export default function Home() {
  const [pokemons, setPokemons] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchAllPokemon() {
      try {
        const promises = ALL_POKEMON_IDS.map((id) =>
          fetch(`https://pokeapi.co/api/v2/pokemon/${id}`).then((res) => res.json())
        );
        const results = await Promise.all(promises);
        setPokemons(results);
        setLoading(false);
      } catch (error) {
        console.error("Failed to fetch Pokémon:", error);
        setLoading(false);
      }
    }
    fetchAllPokemon();
  }, []);

  if (loading) {
    return (
      <div style={{ padding: "24px", textAlign: "center" }}>
        <h1 style={{ color: "#999999" }}>Loading all {ALL_POKEMON_IDS.length} Pokemon…</h1>
        <p style={{ color: "#666666" }}>This might take up to a minute. Please wait.</p>
      </div>
    );
  }

  return (
    <div style={{ padding: "24px", fontFamily: "sans-serif", maxWidth: "1200px", margin: "auto" }}>
      <h1 style={{
        textAlign: "center", marginBottom: "24px", color: "#ffffff",
        fontWeight: "bold", fontSize: "50px"
      }}>
        Pokedex API Next.js Laboratory
      </h1>
      <div style={{ display: "flex", flexWrap: "wrap", justifyContent: "center", gap: "24px" }}>
        {pokemons.map((p) => (
          <div
            key={p.id}
            style={{
              border: "2px solid #dddddd",
              borderRadius: "16px",
              padding: "16px",
              width: "200px",
              textAlign: "center",
              boxShadow: "0 4px 8px #00000014",
              background: "#ffffff",
              position: "relative",
            }}
          >
            {/*#*/}
            <p style={{
              position: "absolute", top: "8px", left: "12px",
              margin: 0, color: "#999999", fontWeight: 700, fontSize: "0.9rem"
            }}>
              {String(p.id).padStart(4, "0")}
            </p>

            {/*pics from github*/}
            <img
              src={p.sprites.other["official-artwork"].front_default}
              alt={p.name}
              width="120"
              style={{ display: "block", margin: "0 auto 8px" }}
            />

            {/*name*/}
            <h2 style={{
              margin: "0 0 10px", fontSize: "1.2rem", fontWeight: 700,
              color: "#999999", textTransform: "capitalize"
            }}>
              {p.name}
            </h2>

            {/*type badges*/}
            <div style={{ display: "flex", justifyContent: "center", gap: "6px", marginBottom: "10px" }}>
              {p.types.map((t: any) => (
                <span
                  key={t.type.name}
                  style={{
                    backgroundColor: typeColors[t.type.name] || "#cccccc",
                    color: "white",
                    padding: "3px 12px",
                    borderRadius: "20px",
                    fontSize: "0.75rem",
                    fontWeight: 700,
                    textTransform: "uppercase",
                  }}
                >
                  {t.type.name}
                </span>
              ))}
            </div>

            {/*height & weight*/}
            <div style={{
              fontSize: "0.75rem", color: "#555555",
              borderTop: "1px solid #eeeeeedd", paddingTop: "8px"
            }}>
              <span style={{ marginRight: "12px" }}>
                ⬆ {(p.height / 10).toFixed(1)} m
              </span>
              <span>⚖ {(p.weight / 10).toFixed(1)} kg</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
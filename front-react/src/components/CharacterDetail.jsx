import { useEffect, useState } from 'react'

function resolveImageUrl(imagePath, apiBaseUrl) {
  if (!imagePath) return null
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }

  const cleanBaseUrl = apiBaseUrl.replace(/\/$/, '')
  const cleanPath = imagePath.startsWith('/') ? imagePath : `/${imagePath}`

  // Construit une URL absolue vers l'image hebergee par Symfony.
  return `${cleanBaseUrl}${cleanPath}`
}

function buildStats(character) {
  return [
    { label: 'STR', value: Number(character.strength ?? 0) },
    { label: 'DEX', value: Number(character.dexterity ?? 0) },
    { label: 'CON', value: Number(character.constitution ?? 0) },
    { label: 'INT', value: Number(character.intelligence ?? 0) },
    { label: 'WIS', value: Number(character.wisdom ?? 0) },
    { label: 'CHA', value: Number(character.charisma ?? 0) },
  ]
}

function getClassName(character) {
  return character.characterClass?.name || character.class?.name || 'Classe inconnue'
}

function getRaceName(character) {
  return character.race?.name || 'Race inconnue'
}

function CharacterDetail({ characterId, apiBaseUrl, onBack, onNavigateToGroups }) {
  const [character, setCharacter] = useState(null)
  const [skills, setSkills] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    const controller = new AbortController()

    async function loadCharacterDetail() {
      setIsLoading(true)
      setError('')
      setSkills([])

      try {
        // 1) Charge le detail principal du personnage.
        const response = await fetch(`${apiBaseUrl}/api/v1/characters/${characterId}`, {
          signal: controller.signal,
        })

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`)
        }

        const detail = await response.json()
        setCharacter(detail)

        // 2) Charge les competences via la classe du personnage.
        const classId = detail.class?.id || detail.characterClass?.id
        if (classId) {
          const classResponse = await fetch(`${apiBaseUrl}/api/v1/classes/${classId}`, {
            signal: controller.signal,
          })

          if (classResponse.ok) {
            const classDetail = await classResponse.json()
            setSkills(Array.isArray(classDetail.skills) ? classDetail.skills : [])
          }
        }
      } catch (err) {
        if (err.name !== 'AbortError') {
          setError('Impossible de charger le detail du personnage.')
        }
      } finally {
        setIsLoading(false)
      }
    }

    loadCharacterDetail()

    return () => controller.abort()
  }, [characterId, apiBaseUrl])

  if (isLoading) {
    return <p className="section-text">Chargement du detail...</p>
  }

  if (error) {
    return <p className="is-invalid">{error}</p>
  }

  if (!character) {
    return <p className="section-text">Personnage introuvable.</p>
  }

  const className = getClassName(character)
  const raceName = getRaceName(character)
  const imageUrl = resolveImageUrl(character.image || character.imageUrl || character.avatar, apiBaseUrl)
  const parties = Array.isArray(character.parties) ? character.parties : []
  const stats = buildStats(character)

  return (
    <section className="panel home-section" id="character-detail">
      <button type="button" className="button button-secondary" onClick={onBack}>
        Retour a la liste
      </button>

      <div className="detail-layout" style={{ marginTop: '1rem' }}>
        <article className="detail-card">
          {imageUrl ? (
            <img className="detail-avatar" src={imageUrl} alt={`Avatar de ${character.name}`} />
          ) : (
            <div className="character-card-placeholder detail-avatar">Aucun avatar</div>
          )}

          <h2 style={{ marginTop: '1rem' }}>{character.name}</h2>
          <p className="section-text">
            {className} - {raceName} - Niveau {character.level ?? '-'}
          </p>
          <p className="section-text">Points de vie: {character.healthPoints ?? '-'}</p>
        </article>

        <article className="detail-card">
          <h3>Statistiques</h3>
          <div className="stats-list">
            {stats.map((stat) => {
              // Barre visuelle basee sur une echelle 0-20.
              const width = `${Math.max(0, Math.min(100, (stat.value / 20) * 100))}%`
              return (
                <div className="stat-row" key={stat.label}>
                  <p>{stat.label}</p>
                  <div className="progress-track" aria-hidden="true">
                    <span className="progress-value" style={{ width }}></span>
                  </div>
                  <p>{stat.value}</p>
                </div>
              )
            })}
          </div>
        </article>

        <article className="detail-card">
          <h3>Competences</h3>
          {skills.length === 0 ? (
            <p className="section-text">Aucune competence disponible.</p>
          ) : (
            <ul className="simple-list">
              {skills.map((skill) => (
                <li key={skill.id}>
                  <span>{skill.name}</span>
                  <strong>{skill.ability}</strong>
                </li>
              ))}
            </ul>
          )}
        </article>

        <article className="detail-card">
          <h3>Groupes d'aventure</h3>
          {parties.length === 0 ? (
            <p className="section-text">Ce personnage n'appartient a aucun groupe.</p>
          ) : (
            <ul className="simple-list">
              {parties.map((party) => (
                <li key={party.id}>
                  <a
                    href="#groups"
                    className="text-link"
                    onClick={(event) => {
                      event.preventDefault()
                      onNavigateToGroups(party.id)
                    }}
                  >
                    {party.name}
                  </a>
                </li>
              ))}
            </ul>
          )}
        </article>
      </div>
    </section>
  )
}

export default CharacterDetail

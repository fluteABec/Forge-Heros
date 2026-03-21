import { useEffect, useState } from 'react'
import GroupCard from './GroupCard'
import GroupDetail from './GroupDetail'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

function normalizeGroups(payload) {
  if (Array.isArray(payload)) return payload
  if (Array.isArray(payload?.items)) return payload.items
  if (Array.isArray(payload?.data)) return payload.data
  return []
}

function GroupsPage({ externalSelectedGroupId, onNavigateToCharacter }) {
  const [groups, setGroups] = useState([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')
  const [showAvailableOnly, setShowAvailableOnly] = useState(false)
  const [selectedGroupId, setSelectedGroupId] = useState(null)

  useEffect(() => {
    if (externalSelectedGroupId !== undefined) {
      setSelectedGroupId(externalSelectedGroupId)
    }
  }, [externalSelectedGroupId])

  useEffect(() => {
    const controller = new AbortController()

    async function loadGroups() {
      setIsLoading(true)
      setError('')

      try {
        const response = await fetch(`${API_BASE_URL}/api/v1/parties`, {
          signal: controller.signal,
        })

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`)
        }

        const json = await response.json()
        setGroups(normalizeGroups(json))
      } catch (err) {
        if (err.name !== 'AbortError') {
          setError('Impossible de charger les groupes pour le moment.')
        }
      } finally {
        setIsLoading(false)
      }
    }

    loadGroups()

    return () => controller.abort()
  }, [])

  const filteredGroups = groups.filter((group) => {
    if (!showAvailableOnly) return true
    const memberCount = Number(group.memberCount ?? 0)
    const maxSize = Number(group.maxSize ?? 0)
    return memberCount < maxSize
  })

  if (selectedGroupId !== null) {
    return (
      <GroupDetail
        groupId={selectedGroupId}
        apiBaseUrl={API_BASE_URL}
        onBack={() => setSelectedGroupId(null)}
        onNavigateToCharacter={onNavigateToCharacter}
      />
    )
  }

  return (
    <section className="panel home-section" id="groups">
      <p className="eyebrow">Groupes</p>
      <h2>Liste des groupes d'aventure</h2>

      <div className="filters" aria-label="Filtres des groupes">
        <label>
          <span>Afficher seulement les groupes avec des places disponibles</span>
          <select
            value={showAvailableOnly ? 'available' : 'all'}
            onChange={(event) => setShowAvailableOnly(event.target.value === 'available')}
          >
            <option value="all">Tous les groupes</option>
            <option value="available">Places disponibles uniquement</option>
          </select>
        </label>
      </div>

      {isLoading && <p className="section-text">Chargement des groupes...</p>}

      {error && !isLoading && <p className="is-invalid">{error}</p>}

      {!isLoading && !error && groups.length === 0 && (
        <p className="section-text">Aucun groupe a afficher.</p>
      )}

      {!isLoading && !error && groups.length > 0 && filteredGroups.length === 0 && (
        <p className="section-text">Aucun groupe avec places disponibles.</p>
      )}

      {!isLoading && !error && filteredGroups.length > 0 && (
        <div className="card-grid" style={{ marginTop: '1rem' }}>
          {filteredGroups.map((group) => (
            <GroupCard key={group.id} group={group} onSelect={setSelectedGroupId} />
          ))}
        </div>
      )}
    </section>
  )
}

export default GroupsPage

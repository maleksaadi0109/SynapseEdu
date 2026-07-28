---
name: ai-rag-essay-eval
description: >-
  Provides implementation patterns for Retrieval-Augmented Generation (RAG) using Laravel AI SDK, 
  pgvector embeddings, semantic search, automated essay evaluation, and Fleiss' Kappa statistical 
  agreement validation.
---

# AI RAG & Essay Evaluation Skill (Laravel AI SDK & Fleiss' Kappa)

Use this skill when building or testing AI-powered features, vector search, essay scoring, or statistical validation modules for EduBridge AI.

---

## 1. Vector Search & RAG Architecture (Laravel AI SDK)

- **Vector Database**: PostgreSQL using `pgvector`.
- **Embeddings Table**:
  - `document_chunks` table containing `id`, `lesson_id`, `content`, `embedding` (vector type), `metadata` (JSONB).
- **Ingestion Pipeline**:
  - Split educational materials (lessons, guides) into ~500-token chunks with overlap.
  - Generate embeddings using Laravel AI SDK.
  - Index embeddings with IVFFlat or HNSW index in PostgreSQL for fast vector similarity search.
- **RAG Retrieval Flow**:
  1. Receive student query.
  2. Generate vector embedding for user query.
  3. Perform cosine distance query against `document_chunks` in PostgreSQL.
  4. Inject retrieved chunks into system prompt as verified context to prevent AI hallucination.

---

## 2. Automated AI Essay Evaluation

- **Rubric Structure**:
  - Grade criteria: Clarity, Argumentation, Grammar, Technical Accuracy, Formatting.
  - Structured output schema (JSON):
    ```json
    {
      "overall_score": 85,
      "breakdown": {
        "clarity": 18,
        "argumentation": 26,
        "grammar": 19,
        "technical_accuracy": 22
      },
      "feedback": "Detailed constructive feedback...",
      "suggestions": ["Improve thesis statement clarity", "Fix subject-verb agreement in paragraph 2"]
    }
    ```

---

## 3. Fleiss' Kappa Statistical Validation Engine

- **Purpose**: Measure inter-rater agreement between multiple human graders and AI evaluation scores to guarantee grading reliability.
- **Formula**:
  \[
  \kappa = \frac{\bar{P} - \bar{P}_e}{1 - \bar{P}_e}
  \]
  where \(\bar{P}\) is the mean proportion of agreeing rating pairs across items, and \(\bar{P}_e\) is the chance agreement probability across rating categories.

- **PHP Implementation Rules**:
  - Create a dedicated validation service: `App\Services\Statistical\FleissKappaCalculator`.
  - Group evaluation categorical ratings into discrete rating bands (e.g. 5 categories: Excellent, Good, Average, Below Average, Poor).
  - Compute \(\bar{P}\) and \(\bar{P}_e\) over a matrix \(N \times K\) (where \(N\) is total evaluated essays, and \(K\) is number of rating categories).
  - Output Kappa score interpretation:
    - \(\kappa < 0.40\): Poor / Fair agreement (Flag AI model for recalibration).
    - \(0.40 \le \kappa < 0.75\): Moderate / Good agreement.
    - \(\kappa \ge 0.75\): Excellent agreement (Trustworthy AI assistance).

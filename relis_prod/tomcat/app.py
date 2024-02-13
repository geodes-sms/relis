from fastapi import FastAPI
import os
from fastapi.responses import JSONResponse, FileResponse
import uvicorn

app = FastAPI(docs_url='/apis/tomcat/docs')


@app.get("/apis/tomcat/list_projects")
def list_projects():
    # Your code to list projects goes here
    try:
        response = {}
        projects = os.listdir("/u/relis/public_html/workspace/dslforge_workspace")
        for project in projects:
            response[project.strip()] = {
                "files": [
                    i
                    for i in os.listdir(
                        f"/u/relis/public_html/workspace/dslforge_workspace/{project.strip()}/src-gen"
                    )
                    if i.endswith(".php")
                ]
            }
        return JSONResponse(response)
    except Exception as e:
        return JSONResponse({"error": str(e)})


@app.get("/apis/tomcat/get_project_configuration")
def get_project_configuration(project_name: str, file_name: str):
    # Your code to get project configuration goes here
    try:
        return FileResponse(
            path=f"/u/relis/public_html/workspace/dslforge_workspace/{project_name}/{file_name}",
            media_type="text/plain",
            filename=file_name,
        )
    except Exception as e:
        return JSONResponse({"error": str(e)})


if __name__ == "__main__":
    uvicorn.run(app, port=8181, log_level="info")
